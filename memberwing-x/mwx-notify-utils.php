<?php

/* **************************************************************************

   Payment notification helpers

This software is provided "as is" without any express or implied warranties,
including, but not limited to, the implied warranties of merchantibility and
fitness for any purpose.
In no event shall the copyright owner, website owner or contributors be liable
for any direct, indirect, incidental, special, exemplary, or consequential
damages (including, but not limited to, procurement of substitute goods or services;
loss of use, data, rankings with any search engines, any penalties for usage of
this software or loss of profits; or business interruption) however caused and
on any theory of liability, whether in contract, strict liability, or
tort(including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.
To request source code for MemberWing please contact http://www.memberwing.com/contact
************************************************************************** */

//===========================================================================
// Force-resets values of $_inputs to defaults
function MWX__ResetInputs (&$_inputs)
{
   $inputs_defaults = array (
      'custom'                   => array('evt'=>''),
      'item_name'                => 'Unknown Item Name',
      'first_name'               => '',
      'last_name'                => '',
      'payer_email'              => '',
      'payer_id'                 => '',
      'subscr_id'                => '0',
      'subscr_date'              => 'now',
      'U_txn_date'               => date ('Y-m-d H:i:s T', strtotime ('now')),
      'recurring'                => '',
      'period3'                  => '',
      'payment_status'           => 'unknown',
      'mc_amount3_gross'         => '0',
      'mc_currency'              => '',
      'txn_id'                   => '',
      'parent_txn_id'            => '',
      'txn_type'                 => '',
      'receiver_email'           => '',
      'pdc_secret'               => '',
      'customer_ip'              => '',
      'referred_by_id'           => 'self',
      'pay_key'                  => '',
      'aff_paid'                 => '',
      'aff_refunded'             => '',
      'paypal_sandbox_enabled'   => '',
      'is_sandbox'               => '',
      'desired_username'         => '',
      'desired_password'         => '',
      );

   foreach ($inputs_defaults as $k=>$v)
      {
      $_inputs[$k] = $v;
      }
}
//===========================================================================

//===========================================================================
//
// This is called from IPN notification after all $_inputs are initialized.

function MWX__TransactionTypeSwitch ()
{
   global $_inputs;

   // Fix urldecoded email addresses that contained '+' in it.
   $_inputs['payer_email'] = str_replace (' ', '+', $_inputs['payer_email']);

   // check the payment_status is Completed
   // check that txn_type+txn_id has not been previously processed
   // check that receiver_email is your Primary PayPal email
   // check that payment_amount/payment_currency are correct - 'mc_gross'(will not present for txn_type=subscr_[signup|cancel|modify|failed|eot]), 'mc_currency', 'item_name', 'item_number'.
   // process payment
   ///!!! NOTE: check e-check payments stuff - delay / ignore?

   switch ($_inputs['txn_type'])
      {
      case "cart":                  // One time payment/purchase
      case "ejgift":                // e-junkie 100% off coupon.
      case "web_accept":
      case "express_checkout" :
      case "mass_pay" :
      case "send_money" :
      case "virtual_terminal" :
      case "A_pay" :                // Adaptive transaction - single item payment.
      case "one_time_pay" :
      case "SALE":                  // Clickbank: The purchase of a standard product or the initial purchase of recurring billing product.
      case "TEST_SALE":             // Clickbank: test sale
      case "UNCANCEL-REBILL":       // Clickbank: Reversing the cancellation of a recurring billing product. Re-add user to premiums. MW 4.x for this case called NU__Add_New_User();
      case 'ORDER_CREATED':         // 2CO
      case 'RECURRING_RESTARTED':   // 2CO
         if ($_inputs['payment_status'] == 'completed')
            {
            if (@$_inputs['custom']['evt'] == 'aff_payout')
               {
               // Not a purchase - Manual affiliate payout
               MWX__log_event (__FILE__, __LINE__, "Processing event: successfully completed manual affiliate payout");
               MWX__Manual_Affiliate_Payout ();
               }
            else
               {
               ///!!! validate receiver email here to avoid spoofing.
               ///!!! if 'tsa' in custom data is present - validate purchase price against it to avoid spoofing. Create 2 calls: GEtIPNSaleAmt()(data passed by paypal) and GetCustomDataSaleAmt()(data passed in custom data)
               MWX__log_event (__FILE__, __LINE__, "Success1: Received one time payment from {$_inputs['payer_email']} for {$_inputs['item_name']}.");
               MWX__Product_Purchased ();  // Single item or subscription purchased. Enter product in user's metadata.
               MWX__Payment_Received ();   // Track payment for affiliate purposes;
               }
            }
         else
            MWX__log_event (__FILE__, __LINE__, "Alert: payment_status='{$_inputs['payment_status']}' for one time payment from {$_inputs['payer_email']} for {$_inputs['item_name']}. Need payment_status='completed' to process user. Nothing done.");
         break;

      case "subscr_signup":   // Subscription creation - 2nd Notification
      case "recurring_payment_profile_created":
         MWX__log_event (__FILE__, __LINE__, "Success1: New subscription signup from {$_inputs['payer_email']} for {$_inputs['item_name']}.");
         MWX__Product_Purchased ();  // Single item or subscription purchased. Enter product in user's metadata.
         break;

      case "subscr_payment":  // Regular subscription payment - 1st notification
      case "recurring_payment":
      case "BILL":            // Clickbank: Recurring installment
      case 'RECURRING_INSTALLMENT_SUCCESS':  // 2CO
         MWX__log_event (__FILE__, __LINE__, "Installment payment processed for {$_inputs['payer_email']} for {$_inputs['item_name']}.");
         MWX__Payment_Received ();   // Track payment for affiliate purposes;
         break;   // For subscriptions, only "subscr_signup" will be allowed to add new user.

      case "subscr_cancel":   // Subscription cancelled prematurely
         MWX__log_event (__FILE__, __LINE__, "Cancel-1: Cancellation request received for subscription '{$_inputs['item_name']}' from '{$_inputs['payer_email']}'");
         // Whole subscription was cancelled in one shot regardless of how many successful recurring payments were already made.
         MWX__Subscription_Cancelled ();
         break;

      case "subscr_eot":      // Subscription ended normally.
      case "recurring_payment_expired":
      case 'RECURRING_COMPLETE': // 2CO
         MWX__log_event (__FILE__, __LINE__, "Cancel-1: Subscription ended normally for '{$_inputs['payer_email']}'");
         MWX__Subscription_Ended ();  // Subscription ended normally.
         break;

      case "refund":
      case "A_refund":
      case "RFND":                           // Clickbank: Refund
      case "CGBK":                           // Clickbank: Chargeback for any payment
      case "INSF":                           // Clickbank: Chargeback for eCheck
      case "CANCEL-REBILL":                  // Clickbank: The cancellation of a recurring billing product. Recurring billing products that are canceled do not result in any other action.
      case 'REFUND_ISSUED':                  // 2C0
      case 'RECURRING_INSTALLMENT_FAILED':   // 2C0
      case 'RECURRING_STOPPED':              // 2C0
         MWX__log_event (__FILE__, __LINE__, "Refund: Refund request for: '{$_inputs['payer_email']}' for product: '{$_inputs['item_name']}'");
         MWX__Payment_Cancelled ();
         break;

      case "__fraud_detected":   // 2C0 / fraud detected.
         MWX__log_event (__FILE__, __LINE__, "Fraud detected for: '{$_inputs['payer_email']}' for product: '{$_inputs['item_name']}'");
         MWX__Payment_Cancelled ();
         break;

      default:
         MWX__log_event (__FILE__, __LINE__, "Note: Received Unknown txn_type={$_inputs['txn_type']} from {$_inputs['payer_email']} for item: '{$_inputs['item_name']}'");
         break;
      }

}
//===========================================================================

//===========================================================================
//
// Determine valid email address of administrator.

function MWX__Get_Admin_Email ()
{
   $admin_email = get_option('admin_email');

   if (!$admin_email)
      $admin_email = "webmaster@" . preg_replace ('#^(www\.)?(.*)$#', "$2", $_SERVER['HTTP_HOST']);

   return ($admin_email);
}
//===========================================================================

//===========================================================================
// 'subscr_signup' or 'cart'-type notification. New product purchased.

function MWX__Product_Purchased ()
{
   global $_inputs;

   // Sanitize username and actual password.
   // Create new user and assign him to requested level.
   // Make sure username will be unique, and add it to WP dbase.
   $i=1;
   $actual_username = $_inputs['desired_username']?$_inputs['desired_username']:$_inputs['payer_email'];
   $actual_username = str_replace (' ', '', $actual_username);
   $actual_username = str_replace ('+', '', $actual_username);


   while (username_exists($actual_username))
      {
      $actual_username = $_inputs['desired_username'] . $i++;
      }

   if ($_inputs['desired_password'])
      $actual_password = $_inputs['desired_password'];
   else
      $actual_password = substr(md5(microtime()), -8);  // If user did not specified password - generate random 8-chars password.

   $blog_root_url  = rtrim(get_bloginfo ('wpurl'), '/');
   $blog_login_url = $blog_root_url . '/wp-login.php?redirect_to=/' . preg_replace ('|^.*?((/)([^\.]+))?$|', "$3", $blog_root_url);

   $admin_email = MWX__Get_Admin_Email ();

   // See if user already exists.
   $user_id = MWX__email_exists ($_inputs['payer_email']);

   // Generate email body. Process these variables:
   // {FIRST_NAME}, {LAST_NAME}, {ITEM_NAME}, {USERNAME}, {PASSWORD}, {BLOG_ROOT_URL}, {BLOG_LOGIN_URL}
   if ($user_id)
      {
      // User already exists - use existing credentials.
      $user_data = get_userdata ($user_id);
      $actual_username = $user_data->user_login;

      // Validate user password for existing Wordpress account
      $wp_user = new WP_User ($user_id);
      if (!wp_check_password ($actual_password, $wp_user->user_pass, $user_id))
         $actual_password = '(EXISTING-PASSWORD)';
      }

   $mwx_settings = MWX__get_settings ();
   $welcome_email_subject = $mwx_settings ['welcome_email_subject'];
   $welcome_email_body = $mwx_settings ['welcome_email_body'];
   $welcome_email_body = preg_replace ('|\{FIRST_NAME\}|', $_inputs['first_name'],  $welcome_email_body);
   $welcome_email_body = preg_replace ('|\{LAST_NAME\}|', $_inputs['last_name'],    $welcome_email_body);
   $welcome_email_body = preg_replace ('|\{ITEM_NAME\}|', $_inputs['item_name'],    $welcome_email_body);
   $welcome_email_body = preg_replace ('|\{BLOG_LOGIN_URL\}|', $blog_login_url,     $welcome_email_body);
   $welcome_email_body = preg_replace ('|\{BLOG_ROOT_URL\}|', $blog_root_url,       $welcome_email_body);
   $welcome_email_body = preg_replace ('|\{USERNAME\}|', $actual_username,          $welcome_email_body);
   $welcome_email_body = preg_replace ('|\{PASSWORD\}|', $actual_password,          $welcome_email_body);

   if ($user_id)
      {
      // Duplicate transaction or duplicate subscription creation event (already existing 'subscr_id') check here.
      if (!MWX__Duplicate_Product_Transaction ($user_id, $_inputs['subscr_id'], $_inputs['txn_id']))
         {
         if ($user_data && user_can ($user_data->ID, 'manage_options'))
            {
            // User is admin already. Probably admin is testing with his own account...
            // Nothing to do
            MWX__log_event (__FILE__, __LINE__, "Success2: Admin(email={$_inputs['payer_email']}) decided to buy his own product: '{$_inputs['item_name']}' for the joy of it. Nothing to do");

            // Notify admin
            MWX__send_email (
               $admin_email,  // To
               $admin_email,  // From
               "Site administrator tried to buy his own product: '{$_inputs['item_name']}'",
               "Admin - {$_inputs['first_name']} {$_inputs['last_name']} ({$_inputs['payer_email']}) tried to buy his own product: '{$_inputs['item_name']}'. Request ignored."
               );
            }
         else
            {
            // New product purchase by existing non-admin
            MWX__log_event (__FILE__, __LINE__, "Success2: Product purchase: '{$_inputs['item_name']}' made by existing member: email={$_inputs['payer_email']}, L/P: $actual_username/$actual_password");

            //--------------------------------------------
            // Add new product purchased to user's metadata.
            // Global $_inputs is used to pull product info
            MWX__Add_New_Transaction_For_User ($user_id);
            //--------------------------------------------

            if (empty($_inputs['__user_info'][$_inputs['payer_email']]['welcome_email_sent']))
               {
               // Prevent delivery of duplicate emails
               $_inputs['__user_info'][$_inputs['payer_email']]['welcome_email_sent'] = true;

               // Notify registered user
               //
               MWX__send_email (
                  $_inputs['payer_email'],  // To
                  $admin_email,  // From
                  $welcome_email_subject,
                  $welcome_email_body
                  );

               // Notify admin
               MWX__send_email (
                  $admin_email,  // To
                  $admin_email,  // From
                  "Existing subscriber: {$_inputs['payer_email']}($actual_username) made a purchase",
                  "{$_inputs['first_name']} {$_inputs['last_name']} ({$_inputs['payer_email']}) ($actual_username / $actual_password) purchased '{$_inputs['item_name']}'"
                  );
               }

            // Add user to autoresponders
            MWX__AddUserToAutoresponder ($user_id, $mwx_settings, $_inputs['item_name']);
            }
         }
      else
         {
         // Duplicate transaction detected
         MWX__log_event (__FILE__, __LINE__, "WARNING: Duplicate transaction or duplicate subscription detected for existing user: email={$_inputs['payer_email']}, subscr_id={$_inputs['subscr_id']}. \nPossibly recurring payment IPN came ahead of subscription creation. Ignoring...");
         }
      }
   else
      {
      // User does not exist - create new one.
      MWX__log_event (__FILE__, __LINE__, "Success2: Registering new user. email: {$_inputs['payer_email']}, L/P: $actual_username/$actual_password");

      // this global variable prevents a double call to subscribe on purchase due to a call attached to registration
      global $in_process_purchase;
      $in_process_purchase = true;
      $user_id = MWX__wp_create_user ($actual_username, $actual_password, $_inputs['payer_email']);
      if (!$user_id)
         {
         MWX__log_event (__FILE__, __LINE__, "ERROR: Cannot create user: '$actual_username' (email: '{$_inputs['payer_email']}')");
         }

      if (@$_inputs['first_name'])
         update_user_meta ($user_id, 'first_name', $_inputs['first_name']);

      if (@$_inputs['last_name'])
         update_user_meta ($user_id, 'last_name', $_inputs['last_name']);

      //--------------------------------------------
      // Add new product purchased to user's metadata.
      // Global $_inputs is used to pull product info
      MWX__Add_New_Transaction_For_User ($user_id);
      //--------------------------------------------

      if (empty($_inputs['__user_info'][$_inputs['payer_email']]['welcome_email_sent']))
         {
         // Prevent delivery of duplicate emails
         $_inputs['__user_info'][$_inputs['payer_email']]['welcome_email_sent'] = true;

         // Notify new user
         //
         MWX__send_email (
            $_inputs['payer_email'],  // To
            $admin_email,  // From
            $welcome_email_subject,
            $welcome_email_body
            );

         // Notify admin
         MWX__send_email (
            $admin_email,  // To
            $admin_email,  // From
            "New user made a purchase: {$_inputs['payer_email']} ($actual_username)",
            "{$_inputs['first_name']} {$_inputs['last_name']} ({$_inputs['payer_email']}) ($actual_username / $actual_password) just purchased '{$_inputs['item_name']}'"
            );
         }

      // Add user to autoresponders
      MWX__AddUserToAutoresponder ($user_id, $mwx_settings, $_inputs['item_name']);
      }

   MWX__log_event (__FILE__, __LINE__, "Success3: Done.");
}
//===========================================================================

//===========================================================================
// Add new product purchased/transaction for existing product to user's metadata.
// Global $_inputs is used to pull product info
// Duplicated transactions will be ignored.
function MWX__Add_New_Transaction_For_User ($user_id)
{
   global $_inputs;

   $products_purchased = MWX__GetListOfProductsForUser ($user_id);
   if (!is_array($products_purchased))
      $products_purchased = array();

   // Get product id. Product id is encoded into the product name (if this is article/page that was purchased: "How to Win(id:25)"
   if (preg_match ('@\(id:(\d+)\)$@', $_inputs['item_name'], $matches))
      $product_id = $matches[1];
   else
      $product_id = "0";

   // See if product already exist on a transaction level - first search for 'subscr_id' then for 'txn_id'.
   // Note: purchase of the same product again will be treated as a separate purchase.
   foreach ($products_purchased as $idx=>$product)
      {
      // in_array ($needle, $haystack_arr);
      if ($_inputs['txn_id'] && in_array ($_inputs['txn_id'], $product['txn_ids']))
         {
         MWX__log_event (__FILE__, __LINE__, "Duplicate transaction detected for already existing product. txn_id='{$_inputs['txn_id']}'. Not adding new transaction. Possible reason: First recurring payment notification from Paypal arrived before new subscription creation notification");
         return;  // Duplicate transaction detected for already existing product
         }

      if ($_inputs['subscr_id'] && $_inputs['subscr_id'] == $product['subscr_id'])
         {
         // Found pre-existing product = subscription. If this is subscription creation again - ignore it.
         if ($_inputs['txn_type'] == 'subscr_signup')
            {
            MWX__log_event (__FILE__, __LINE__, "WARNING: Duplicate subscription creation: subscr_id='{$_inputs['subscr_id']}' already exists. Not adding new subscription.");
            return; // Duplicate new subscription creation
            }

         // Adding new, unique transaction for this product.
         $products_purchased[$idx]['txn_ids'][] = $_inputs['txn_id'];
         $products_purchased[$idx]['product_status'] = 'active';   // Make sure subscription is active when user is paying. It could of been marked as 'inactive' in Payment_Cancelled() call.
         update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
         MWX__log_event (__FILE__, __LINE__, "Added new recurring transaction/payment record for already existing subscription for user: {$_inputs['payer_email']}, subscription: '{$_inputs['item_name']}'.");
         return;
         }
      }

   // This is brand new transaction about brand new product purchase. Process it.

   // Save into user's metadata all kind of information about this purchase
   // array (array('product_id'=>'5', 'product_name'=>'', 'purchase_date'=>'2009-12-02', 'txn_id'=>array(...), 'subscr_id'=>'', 'active'=>'1'), array(...), ...)
   if (isset($_inputs['txn_id']) && $_inputs['txn_id'])
      $txn_ids = array ($_inputs['txn_id']);
   else
      $txn_ids = array ();

   $product_name = str_replace ("'", "", $_inputs['item_name']);

   $products_purchased[] =
      array (
         'product_id'      => $product_id,
         'product_name'    => $product_name,
         'purchase_date'   => $_inputs['U_txn_date'],
         'full_sale_amt'   => $_inputs['mc_amount3_gross'],
         'expiry_date'     => MWX__GetProductExpiryDate ($product_name, $_inputs['U_txn_date']),
         'txn_ids'         => $txn_ids,
         'subscr_id'       => $_inputs['subscr_id'],
         'referred_by_id'  => $_inputs['referred_by_id'],   // Affiliate's id (user_id) who refered this purchase
         'product_status'  => 'active',      // 'active'(customer is in good standing), 'cancelled'(subscription), 'ended'(subscription ended normally), 'expired'(forced expiry date reached), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
         );

   update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
   MWX__log_event (__FILE__, __LINE__, "Added new product/transaction for user: {$_inputs['payer_email']}, product: '{$_inputs['item_name']}'.");
}
//===========================================================================

//===========================================================================
// Track payment for affiliate purposes.

function MWX__Payment_Received ()
{
   global $_inputs;
   $aff_email_sent = FALSE;

   //--------------------------------------------
   // Make sure buyer exists as a member of blog before processing any affiliate stuff.
   $user_id = MWX__email_exists ($_inputs['payer_email']);
   if (!$user_id)
      {
      MWX__log_event (__FILE__, __LINE__, "Note: buyer '{$_inputs['payer_email']}' of product '{$_inputs['item_name']}' does not yet exist. Creating him...");
      MWX__Product_Purchased (); // Create new user for this payment. It is possible that subscription payment came ahead of subscription creation. In this case we need to call this.
      $user_id = MWX__email_exists ($_inputs['payer_email']);
      }

   if (!$user_id)
      {
      MWX__log_event (__FILE__, __LINE__, "ERROR: cannot add buyer '{$_inputs['payer_email']}' of product '{$_inputs['item_name']}' for some reason. Affiliate commisions is not processed. Aborting payment notification processing.");
      return;
      }
   //--------------------------------------------

   $mwx_settings = MWX__get_settings ();

   // Check if static affiliate referrer ID is set and non-empty (this is the one that possibly came through invitation code).
   // If it is - it will override possibly present dynamic affiliate referrer id.
   $mwx_extra_user_data = MWX__get_usermeta_array ($user_id, 'mwx_extra_user_data');
   if (isset($mwx_extra_user_data['referred_by_id']) && $mwx_extra_user_data['referred_by_id'])
      {
      if ($_inputs['referred_by_id'])
         MWX__log_event (__FILE__, __LINE__, "NOTE: Overriding currently present dynamic affiliate referrer ID '{$_inputs['referred_by_id']}' with static one (that came with an invitation code): '{$mwx_extra_user_data['referred_by_id']}'");
      else
         MWX__log_event (__FILE__, __LINE__, "NOTE: Force-setting affiliate referrer ID to static ID (that came with an invitation code): '{$mwx_extra_user_data['referred_by_id']}'");

      // Overwrite dynamic affiliate id with static one
      $_inputs['referred_by_id'] = $mwx_extra_user_data['referred_by_id'];
      }

   //--------------------------------------------
   // Record new (only if unique) transaction/subscription in user metadata.
   // We need to do it here because for new subscription IPN about payment might arrive before IPN about 'subscr_signup'
   // Duplicated transactions will be ignored.
   MWX__Add_New_Transaction_For_User ($user_id);
   //--------------------------------------------


   // Check if aff id is not the same as seller's email.
   if ($_inputs['referred_by_id'] == $_inputs['receiver_email'])
      $_inputs['referred_by_id'] = 'self';

   if ($mwx_settings['mwx_affiliate_network_enabled'] && $_inputs['referred_by_id'] && $_inputs['referred_by_id'] != 'self')
      {
      // Record sale by affiliate within MWX Integrated Affiliate Network
      if (@$_inputs['txn_id'])
         {
         $aff_raw_ids = explode (',', $_inputs['referred_by_id']);

         foreach ($aff_raw_ids as $tier=>$aff_raw_id)
            {
            if (!$aff_raw_id)
               // Zero affiliate ID means it was erased from payment chain (in mwx-paypal-x.php). Possibly old link contained ID of affiliate who has been deactivated. Hence ID was replaced with '0'.
               continue;

            if ($tier >= $mwx_settings['aff_tiers_num'])
               break;   // Do not process more tiers than specified in aff. net. settings, even if custom data (cookie) has more tiers.

            //    array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
            $aff_info = MWX__GetAffiliateInfoByRawID ($aff_raw_id);

            if (isset($aff_info['mwx_aff_info']['aff_status']) && $aff_info['mwx_aff_info']['aff_status'] != 'active')
               {
               // Affiliate already exists but NOT marked as 'active'.
               // This means no transactions will be credited to him until his status will become 'active'.
               MWX__log_event (__FILE__, __LINE__, "Note: Affiliate's {$aff_info['aff_email']} current status is: '{$aff_info['mwx_aff_info']['aff_status']}'. Not crediting him for this sale. His status must be 'active'...");
               continue;
               }

            // See if affiliate already exists as a blog member. If not - add him automatically if auto-approval option is enabled.
            if ((!is_array($aff_info['mwx_aff_info']) || !count($aff_info['mwx_aff_info'])) && $mwx_settings['aff_auto_approve_affiliates'])
               {
               // Add new affiliate user.
               //    Returns array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
               $aff_info = MWX__CreateNewAffiliate ($aff_info['aff_email'], $aff_info['aff_email'], '', $_inputs['is_sandbox'], FALSE);
///               $_inputs['referred_by_id'] = $aff_info['aff_id'];

               MWX__log_event (__FILE__, __LINE__, "Note: Adding new affiliate: {$aff_info['aff_email']}");

               // Dispatch email to affiliate for referred sale
               ///!!! Make it admin options-configurable.
               MWX__send_email (
                  $aff_info['aff_email'],                // To
                  MWX__Get_Admin_Email (),               // From
                  "You just referred your first sale!",  // Subject
                  "<br />You just referred your first sale!" .
                  "<br />Your new affiliate account was automatically created." .
                  "<br />Login URL: " . rtrim(get_bloginfo ('wpurl'), '/') . '/wp-login.php' .
                  "<br />Username : " . $aff_info['aff_username'] .
                  "<br />Password : " . $aff_info['aff_password'] .
                  "<br /><br />Congratulations and thank you!" .
                  "<br /><br />" . get_bloginfo ('wpurl')
                  );
               $aff_email_sent = TRUE;
               }

///            $_inputs['referred_by_id'] = $aff_info['aff_id'];

            // Record referral sale
            // 'aff_info'= array (
            //    'aff_status'=>'active',    // active, pending, declined, banned.
            //    'immune_to_min_payout_limit'=>'0',
            //    'payout_percents'=>'20',
            //    'payout_adjustment'=>'0',  // Outstanding bonus (+) or outstanding payment adjustment (-)  (product refund for already paid commission)
            //    'payouts'=>array( array('date'=>'', 'payout_txn_id'=>'', 'payout_amt'=>''), array(...))
            //    'referrals'[] = array ('txn_id'=>$_inputs['txn_id'], 'txn_date'=>$_inputs['U_txn_date'], 'full_sale_amt'=>'0', 'payout_amt'=>0, 'affiliate_tier'=>1, 'referral_for_id'=>$user_id, 'status'=>'approved', 'paid'=>'0')
            //    )
            if (is_array ($aff_info['mwx_aff_info']) && count($aff_info['mwx_aff_info']))
               {
               // Before adding another transaction for this affiliate - make sure it is not duplicate (not already exist).
               $duplicate_txn = FALSE;
               foreach ($aff_info['mwx_aff_info']['referrals'] as $referral)
                  {
                  if ($referral['txn_id'] == $_inputs['txn_id'])
                     {
                     $duplicate_txn = TRUE;
                     break;
                     }
                  }

               if (!$duplicate_txn)
                  {
                  // Set referral transaction status. Possible values:
                  //    -  'approved' (auto or manually approved),
                  //    -  'declined' (will be set manually. Suspected affiliate frauds/rules violation cases),
                  //    -  'refunded' (original customer was refunded for his purchase by merchant AND this affiliate refunded his commission to merchant (via Adaptive, good-willing manually or not been paid yet anyways case)). This reflects the case when actual commission that was paid and then returned. If commision was not paid yet and product refund happened - the status would be 'reversed'.
                  //    -  'reversed' (original customer was refunded for his purchase by merchant but this affiliate has not been paid commision yet anyways).
                  //    -  'pending'  (manual approval or date limit is present. Once date passed - will be set to 'approved')
                  //    -  'adjusted' (original customer was refunded for his purchase by merchant BUT this affiliate did not return his commission. So adjustment was made to his 'payout_adjustment' param).

                  if ($_inputs['aff_paid'])
                     $aff_txn_status = 'approved';    // Already paid
                  else if ($mwx_settings['aff_manual_aff_sale_approval'] || $mwx_settings['aff_sale_auto_approve_in_days'])
                     $aff_txn_status = 'pending';
                  else if (!$mwx_settings['aff_min_payout_threshold'] || $aff_info['mwx_aff_info']['immune_to_min_payout_limit'])
                     $aff_txn_status = 'approved';
                  else
                     $aff_txn_status = 'pending';

                  $affiliate_payout = MWX__CalculateAffiliatePayoutForSale ($aff_info['aff_id'], $_inputs['mc_amount3_gross'], $aff_info['mwx_aff_info'], $mwx_settings, $tier+1);

                  // New, unique, valid transaction for this affiliate. Record it...
                  $aff_info['mwx_aff_info']['referrals'][] =
                     array (
                        'txn_date'        => $_inputs['U_txn_date'],
                        'txn_id'          => $_inputs['txn_id'],
                        'full_sale_amt'   => $_inputs['mc_amount3_gross'],
                        'payout_amt'      => $affiliate_payout,
                        'affiliate_tier'  => $tier+1,    // 1-main affiliate, 2...5
                        'referral_for_id' => $user_id,
                        'status'          => $aff_txn_status,        // 'approved', 'declined', 'refunded', 'reversed', 'pending', 'adjusted'
                        'paid'            => $_inputs['aff_paid'],   // If Adaptive payment => was paid, else:not paid.
                        );

                  // Update info for this affiliate
                  update_user_meta ($aff_info['aff_id'], 'mwx_aff_info', serialize ($aff_info['mwx_aff_info']));

                  $t = $tier+1;
                  MWX__log_event (__FILE__, __LINE__, "Note: Added new transaction for tier $t affiliate: '{$aff_info['aff_email']}'. Added commission amount: \$$affiliate_payout  for product {$_inputs['item_name']} (\${$_inputs['mc_amount3_gross']}) purchased by {$_inputs['payer_email']}");

                  // Notify affiliate about sale
                  ///!!! Make it admin options-configurable.
                  if (!$aff_email_sent)
                     {
                     MWX__send_email (
                        $aff_info['aff_email'],                // To
                        MWX__Get_Admin_Email (),               // From
                        "You just referred sale!",  // Subject
                        "<br />You just referred a sale and earned $ $affiliate_payout in affiliate commissions." .
                        "<br /><br />Congratulations and thank you!" .
                        "<br /><br />" . get_bloginfo ('wpurl')
                        );
                     }
                  }
               else
                  MWX__log_event (__FILE__, __LINE__, "Warning: Ignoring duplicate transaction for affiliate. (affiliate id = $aff_raw_id, txn_id={$_inputs['txn_id']})");
               }
            else
               MWX__log_event (__FILE__, __LINE__, "Warning: Product sale was referred by non-existing affiliate. Affiliate id: $aff_raw_id. Check if such user ID exists in Admin->Users");
            }
         }
      else
         MWX__log_event (__FILE__, __LINE__, "Warning: MWX__Payment_Received() called while txn_id is not set.");
      }


   /*
   To add support for iDevAffiliate we need to pass IP address of customer making purchase to iDevAff. Do this:

   -  Inside paypal's HTML button code add this line:
      <input type="hidden" name="custom" value="__SERVER__REMOTE_ADDR__">
      (this line will get converted to IP address by MemberWing, and will get passed to this script via $_POST['custom'] variable.
         OR (if this page is not under control of MemberWing *AND* this is .php page):
      <input type="hidden" name="custom" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
   */

   if ($mwx_settings ['idevaffiliate_integration_enabled'] && $mwx_settings ['idevaffiliate_install_dirname'])
      {
      if (!$_inputs['customer_ip'])
         {
         MWX__log_event (__FILE__, __LINE__, "Warning: iDevAffiliate Integration enabled but no customer IP passed to script. Cannot register transaction with iDevAffiliate (payment button code does not include required custom field for affiliate tracking?).");
         }
      else
         {
         // Prepare information for iDevAffiliate.
         //
         $_IDEV_DIRECTORY_URL = $mwx_settings ['idevaffiliate_install_dirname'];
         $_IDEV_ORDER_NUM     = $_inputs['txn_id'];            // 'idev_ordernum' to be the paypal transaction ID number (IdevAff support answer).
         $_IDEV_SALE_AMT      = $_inputs['mc_amount3_gross'];
         $_IDEV_IP_ADDR       = $_inputs['customer_ip'];
         MWX__log_event (__FILE__, __LINE__, "Notifying iDevAffiliate: Directory URL=$_IDEV_DIRECTORY_URL, Order Number=$_IDEV_ORDER_NUM, Sale Amount=$_IDEV_SALE_AMT, IP Addr=$_IDEV_IP_ADDR");
         MWX__notify_idevaffiliate ($_IDEV_DIRECTORY_URL, $_IDEV_SALE_AMT, $_IDEV_ORDER_NUM, $_IDEV_IP_ADDR);
         }
      }
}
//===========================================================================

//===========================================================================
// Whole subscription was cancelled in one shot regardless of how many successful recurring payments were already made.
// This is only called for subscriptions and 'subscr_id' is set and 'txn_id' is NOT set in this case.

function MWX__Subscription_Cancelled ()
{
   global $_inputs;

   $mwx_settings = MWX__get_settings ();

   if (!isset($_inputs['subscr_id']) || !$_inputs['subscr_id'])
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: Cannot process subscription cancellation without valid 'subscr_id' set. Exiting...");
      return; // Cannot do anything without valid 'subscr_id'
      }

   $user_id = MWX__email_exists ($_inputs['payer_email']);
   if (!$user_id)
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: Cannot process subscription cancellation for non-existing buyer email: {$_inputs['payer_email']}. Exiting...");
      return; // Cannot do anything
      }

   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

   if (!is_array($products_purchased))
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: This buyer: {$_inputs['payer_email']} does not seems to own any products according to our records. Exiting...");
      return;  // Cannot do anything
      }

   $admin_email = MWX__Get_Admin_Email ();

   if ($mwx_settings['keep_cancelled_subs_active_till_eot'])
      {
      MWX__log_event (__FILE__, __LINE__, "Keeping access to premium content until the end of term for user: '{$_inputs['payer_email']}'. Will wait for 'subscr_eot' message");

      foreach ($products_purchased as $idx=>$product)
         {
         // Mark product by unique subscription id as "active-ending"
         if ($_inputs['subscr_id'] == $product['subscr_id'])
            {
            // In a future we may need to delete cancelled subscription...
            // unset ($products_purchased[$idx]);  // Remove element from array.
            // $products_purchased = array_values ($products_purchased);   // Reindex array.

            $products_purchased[$idx]['product_status'] = 'active-ending';
            update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));

            MWX__log_event (__FILE__, __LINE__, "User: '{$_inputs['payer_email']}' cancelled subscription for '{$_inputs['item_name']}'. Adjusted his product status to 'active-ending'.");

            // Notify administrator about cancellation...
            MWX__send_email (
               $admin_email,
               $admin_email,
               "Subscription cancelled by {$_inputs['payer_email']}",
               "Subscription cancelled by user: '{$_inputs['payer_email']}' ({$_inputs['first_name']} {$_inputs['last_name']}). Item: {$_inputs['item_name']}. Reason: {$_inputs['txn_type']}. Keeping access active until the end of term."
               );

            return;
            }
         }

      MWX__log_event (__FILE__, __LINE__, "WARNING: Could not find a proper product for this user");
      return;
      }


   // Subscription cancelled and access is terminated immediately regardless if there are any leftovers over the current term.

   foreach ($products_purchased as $idx=>$product)
      {
      // Delete product by unique subscription id.
      if ($_inputs['subscr_id'] == $product['subscr_id'])
         {
         // In a future we may need to delete cancelled subscription...
         // unset ($products_purchased[$idx]);  // Remove element from array.
         // $products_purchased = array_values ($products_purchased);   // Reindex array.

         $products_purchased[$idx]['product_status'] = 'cancelled';
         update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));

         MWX__log_event (__FILE__, __LINE__, "User: '{$_inputs['payer_email']}' cancelled subscription for '{$_inputs['item_name']}'. Adjusted his product status to 'cancelled'.");
         // If user does not own any products (serial refunder) see if we need to delete him.
         if (!count($products_purchased))
            {
            if ($mwx_settings['delete_emptyhanded_user'])
               {
               MWX__log_event (__FILE__, __LINE__, "Removing emptyhanded user: '{$_inputs['payer_email']}'.");
               wp_delete_user ($user_id);
               }
            }

         // Notify administrator about cancellation...
         MWX__send_email (
            $admin_email,
            $admin_email,
            "Subscription cancelled by {$_inputs['payer_email']}",
            "Subscription cancelled by user: '{$_inputs['payer_email']}' ({$_inputs['first_name']} {$_inputs['last_name']}). Item: {$_inputs['item_name']}. Reason: {$_inputs['txn_type']}."
            );

         return;
         }
      }

   MWX__log_event (__FILE__, __LINE__, "Warning: User '{$_inputs['payer_email']}' cancelled subscription for '{$_inputs['item_name']}' but no such subscription on record found for that user.");
   return;
}
//===========================================================================

//===========================================================================
//
// Either single payment for product was refunded/reversed or one of the recurring payments was refunded/reversed.
// In case of single payment product - the action will be the same as 'Purchase_Cancelled' - product will be removed from user's meta.
// In case of recurring payment product (subscription) product will be marked as 'inactive' but will be kept in user's meta in case user re-pay installment.

function MWX__Payment_Cancelled ()
{
   global $_inputs;

   if (!isset($_inputs['parent_txn_id']) || !$_inputs['parent_txn_id'])
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: Cannot process refund without 'parent_txn_id' set. Exiting...");
      return; // Cannot do anything without knowing which transaction was cancelled
      }

   $user_id = MWX__email_exists ($_inputs['payer_email']);
   if (!$user_id)
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: Cannot process refund for non-existing buyer email: {$_inputs['payer_email']}. Exiting...");
      return; // Cannot do anything
      }

   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

   if (!is_array($products_purchased))
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: This buyer: {$_inputs['payer_email']} does not seems to own any products according to our records. Exiting...");
      return;  // Cannot do anything
      }

   // Search for the product that has the transaction that was cancelled...
   $product_found   = FALSE;
   $affiliate_found = FALSE;
   foreach ($products_purchased as $idx=>$product)
      {
      // in_array/array_search ($needle, $haystack);
      $idx2 = array_search ($_inputs['parent_txn_id'], $product['txn_ids']);
      if ($idx2 !== FALSE)
         {
         // Found product/transaction that was cancelled!
         $product_found = TRUE;

         //---------------------------------
         // First - find affiliate who referred this sale. His current commissions need to be adjusted.
         //
         //    array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
         $aff_info = MWX__GetAffiliateInfoByRawID ($product['referred_by_id']);

         // Find this transaction in affiliate's records.
         if (is_array($aff_info['mwx_aff_info']['referrals']))
            {
            foreach ($aff_info['mwx_aff_info']['referrals'] as $refidx=>$referral)
               {
               if ($referral['txn_id'] == $_inputs['parent_txn_id'])
                  {
                  // Found transaction at affiliate's records.
                  $affiliate_found = TRUE;
                  MWX__log_event (__FILE__, __LINE__, "NOTE: Found referring affiliate for this payment: {$aff_info['aff_email']}. txn_id='{$referral['txn_id']}' Will be adjusting affiliate's payment records...");

                  if ($referral['paid'])
                     {
                     if ($_inputs['aff_refunded'])
                        {
                        // Refund was automatically taken from Affiliate via Adaptive Refund Method. This is possible because original purchase was Adaptive/Chained as well and merchant have API approval from affiliate to take money.
                        $aff_info['mwx_aff_info']['referrals'][$refidx]['status']     = 'refunded';
                        $aff_info['mwx_aff_info']['referrals'][$refidx]['payout_amt'] = '0.00';
                        MWX__log_event (__FILE__, __LINE__, "NOTE: Adjusting referral status for /already paid/ affiliate {$aff_info['aff_email']}. txn_id '{$referral['txn_id']}' status set to 'refunded'. Referral was originally made for user '{$_inputs['payer_email']}' who was refunded for '{$_inputs['item_name']}'.");
                        }
                     else
                        {
                        // Affiliate was paid but has not refunded his commission. We have to adjust his 'payout_adjustment' value to compensate for owed commission.
                        $aff_info['mwx_aff_info']['referrals'][$refidx]['status'] = 'adjusted';
                        $aff_info['mwx_aff_info']['payout_adjustment'] = (float)@$aff_info['mwx_aff_info']['payout_adjustment'] - (float)$referral['payout_amt'];
                        $aff_info['mwx_aff_info']['referrals'][$refidx]['payout_amt'] = '0.00';
                        MWX__log_event (__FILE__, __LINE__, "NOTE: Adjusting referral status for /already paid/ affiliate {$aff_info['aff_email']}. txn_id '{$referral['txn_id']}' status set to 'adjusted'. Referral was originally made for user '{$_inputs['payer_email']}' who was refunded for '{$_inputs['item_name']}'.");
                        }
                     }
                  else
                     {
                     // Commission luckily was not paid yet.
                     $aff_info['mwx_aff_info']['referrals'][$refidx]['status']     = 'reversed';
                     $aff_info['mwx_aff_info']['referrals'][$refidx]['payout_amt'] = '0.00';
                     MWX__log_event (__FILE__, __LINE__, "NOTE: Reversing commission for affiliate {$aff_info['aff_email']}. txn_id '{$referral['txn_id']}' status set to 'reversed'. Referral was originally made for user '{$_inputs['payer_email']}' who was refunded for '{$_inputs['item_name']}'.");
                     }

                  // Update this affiliate's data
                  update_user_meta ($aff_info['aff_id'], 'mwx_aff_info', serialize ($aff_info['mwx_aff_info']));
                  break;
                  }
               }
            }
         //---------------------------------

         if ($product['subscr_id'])
            {
            // If this product is a subscription - mark it as inactive until user will fix his finances.

            // Remove cancelled transaction from the list of transactions for this product.
            unset ($products_purchased[$idx]['txn_ids'][$idx2]);
            $products_purchased[$idx]['txn_ids'] = array_values ($products_purchased[$idx]['txn_ids']);  // Reindex array.

            // 'active'(customer is in good standing), 'cancelled'(subscription), 'ended'(subscription ended normally), 'expired'(forced expiry date reached), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
            $products_purchased[$idx]['product_status'] = 'refunded';   // Mark subscription as inactive but keep it in in case user will resume payments for it.
            MWX__log_event (__FILE__, __LINE__, "Alert: Payment cancellation/refund request: Marking subscription to '{$_inputs['item_name']}' as inactive for: {$_inputs['payer_email']}");
            }
         else  // I dont think this extra check is needed at all. If it is not subscription=>one time anyways...  if (!count($products_purchased[$idx]['txn_ids']))
            {
            // In a future we may want to remove the product completely in case of refund. Leave it in with a proper adjusted status.
            //// Remove it.
            ///unset ($products_purchased[$idx]);  // Remove the whole product from user's meta.
            ///$products_purchased = array_values ($products_purchased);   // Reindex array.

            $products_purchased[$idx]['product_status'] = 'refunded';
            MWX__log_event (__FILE__, __LINE__, "Alert: Payment cancellation/refund request: Removing product '{$_inputs['item_name']}' from: {$_inputs['payer_email']}");
            }

         update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));

         // If user does not own any products (serial refunder) see if we need to delete him.
         if (!count($products_purchased))
            {
            $mwx_settings = MWX__get_settings ();
            if ($mwx_settings['delete_emptyhanded_user'])
               {
               MWX__log_event (__FILE__, __LINE__, "Removing emptyhanded user: {$_inputs['payer_email']}");
               wp_delete_user ($user_id);
               }
            }
         return;
         }
      }

   if (!$product_found)
      MWX__log_event (__FILE__, __LINE__, "WARNING: No product found for this transaction: parent_txn_id='{$_inputs['parent_txn_id']}'. Cannot reverse product transaction");

   if (!$affiliate_found)
      MWX__log_event (__FILE__, __LINE__, "NOTE: No affiliates(referrers) found for this transaction: txn_id='{$_inputs['parent_txn_id']}'.");

   return;
}
//===========================================================================

//===========================================================================
//
// Action will depend on admin settings. Access may remain active or be disabled.
function MWX__Subscription_Ended ()
{
   global $_inputs;

   if (!isset($_inputs['subscr_id']) || !$_inputs['subscr_id'])
      return; // Cannot do anything without valid 'subscr_id'

   $user_id = MWX__email_exists ($_inputs['payer_email']);
   if (!$user_id)
      return; // Cannot do anything

   $mwx_settings = MWX__get_settings ();
   if ($mwx_settings['keep_access_for_ended_subscriptions'])
      {
      MWX__log_event (__FILE__, __LINE__, "NOTE: Subscription for '{$_inputs['item_name']}' ended normally for: {$_inputs['payer_email']}. Keeping his access active.");
      return;  // Requested to keep access for subscriptions that are ended normally.
      }

   // Requested to terminate access for ended subscriptions.

   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

   if (!is_array($products_purchased))
      return;  // Cannot do anything

   // Search for subscription record.
   foreach ($products_purchased as $idx=>$product)
      {
      // in_array/array_search ($needle, $haystack);
      if ($_inputs['subscr_id'] == $product['subscr_id'])
         {
         if (@$product['expiry_date'])
            {
            // Custom expiry date is set. Ignore end-of-term event. Status will change to "expired" autmatically as soon as custom expiry will be reached
            MWX__log_event (__FILE__, __LINE__, "NOTE: Custom expiry date is set for this product, ignoring end of term event: {$product['product_name']} : {$product['expiry_date']}.");
            return;
            }

         // 'active'(customer is in good standing), 'cancelled'(subscription), 'ended'(subscription ended normally), 'expired'(forced expiry date reached), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
         if (!$mwx_settings['keep_access_for_ended_subscriptions'])
            $products_purchased[$idx]['product_status'] = 'ended';   // Mark subscription as inactive but keep it in in case user will resume payments for it.

         update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
         MWX__log_event (__FILE__, __LINE__, "Subscription for '{$_inputs['item_name']}' ended normally for: {$_inputs['payer_email']}. Disabling access.");
         return;
         }
      }
}
//===========================================================================

//===========================================================================
// Function checks if this is duplicate transaction or duplicate subscription creation.
//    Note: function does not check for duplicate purchase (same product bought many times), only for duplicate payment transactions.
// Logic:   if 'subscr_id' is not null and already exists => duplicate,
//          if 'txn_id' is not null and already exists => duplicate, else - not.

function MWX__Duplicate_Product_Transaction ($user_id, $subscr_id, $txn_id)
{
   $products_purchased = MWX__GetListOfProductsForUser ($user_id);
   if (!is_array($products_purchased))
      return FALSE;  // No transactions on record for this user.

   foreach ($products_purchased as $product)
      {
      // in_array ($needle, $haystack_arr);
      if ($txn_id && in_array ($txn_id, $product['txn_ids']))
         return TRUE;  // Duplicate transaction detected for already existing product

      if ($subscr_id && $subscr_id == $product['subscr_id'])   // Here we presume that 'txn_type' == 'subscr_signup'
         return TRUE;  // Duplicate subscription creation notification detected.
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
//
// Notify iDevAffiliate software about money coming in.

function MWX__notify_idevaffiliate ($idev_directory_url, $sale_amount, $order_number, $ip_address)
{
   $ch = curl_init();
   curl_setopt ($ch, CURLOPT_URL, $idev_directory_url . "/sale.php?profile=72198&idev_saleamt=$sale_amount&idev_ordernum=$order_number&ip_address=$ip_address");
   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
   $RetCode = curl_exec   ($ch);
   curl_close  ($ch);

   if ($RetCode)
      MWX__log_event (__FILE__, __LINE__, "Successfully notified iDevAffiliate about sale");
   else
      MWX__log_event (__FILE__, __LINE__, "WARNING: Notifying iDevAffiliate PROBLEM: curl_exec returned FALSE");
}
//===========================================================================

//===========================================================================
function MWX__AddUserToAutoresponder ($user_id, $mwx_settings, $item_name = 'default')
{
   global $_inputs;

   if (!isset($_inputs) || !is_array($_inputs))
      {
      $_inputs = array();
      $user_data = get_userdata ($user_id);

      $_inputs['payer_email']       = $user_data->user_email;
      $_inputs['desired_username']  = $user_data->user_login;
      $_inputs['desired_password']  =  "(EXISTING PASSWORD)";
      $_inputs['first_name']        = isset($user_data->first_name)?($user_data->first_name):"";
      $_inputs['last_name']         = isset($user_data->last_name)?($user_data->last_name):"";
      }

   if (!$_inputs['payer_email'])
      return;  // Nothing to do without user's email

   // prepare service details
   $mailchimp_api_key = $mwx_settings ['mailchimp_api_key'];
   $mailchimp_interest_groups = $mwx_settings ['mailchimp_interest_groups'];
   $oneshoppingcart_merchant_id_number = $mwx_settings ['1shoppingcart_merchant_id_number'];

   // go through each autoresponder and subscribe to lists, else subscribe to default list
   $user_subscribed = false;
   $default_assignment = null;
   foreach ((array)@$mwx_settings['autoresponder_assignments'] as $autoresponder_assignment)
      {
     // store default credentials
     if ($autoresponder_assignment['level'] == 'default') $default_assignment = $autoresponder_assignment;
      // check for level/assignment match
     if (stristr ($item_name, $autoresponder_assignment['level']) != false)
        {
         // check if already subscribed
         $user_added_to_autoresponder = get_user_meta ($user_id, 'mwx_user_autoresponded_'.$autoresponder_assignment['key'], true);
       if (!$user_added_to_autoresponder)
       {
          // figure out which service and verify settings
         if ($autoresponder_assignment['service'] == 'Aweber')
            $user_subscribed = MWX_SubscribeToAweber ($autoresponder_assignment);
         else if ($autoresponder_assignment['service'] == 'MailChimp' && $mailchimp_api_key != '')
            $user_subscribed = MWX_SubscribeToMailChimp ($autoresponder_assignment, $mailchimp_api_key, $mailchimp_interest_groups);
         else if ($autoresponder_assignment['service'] == '1ShoppingCart' && $oneshoppingcart_merchant_id_number != '')
            $user_subscribed = MWX_SubscribeTo1ShoppingCart ($autoresponder_assignment, $oneshoppingcart_merchant_id_number);
       }
         else
         {
         MWX__log_event (__FILE__, __LINE__, "NOTE: Not adding user: {$_inputs['payer_email']} to autoresponder {$autoresponder_assignment['key']}. Already been added.");
         }
        if ($user_subscribed) update_user_meta ($user_id, 'mwx_user_autoresponded_'.$autoresponder_assignment['key'], '1');
        }
      }

   // subscribe to default list if no subscription was made above and a default list exists
   if (!$user_subscribed && $default_assignment != null)
      {
      // check if already subscribed
      $user_added_to_autoresponder = get_user_meta ($user_id, 'mwx_user_autoresponded_'.$default_assignment['key'], true);
     if (!$user_added_to_autoresponder)
         {
         // figure out which service and verify settings
         if ($default_assignment['service'] == 'Aweber')
            $user_subscribed = MWX_SubscribeToAweber ($default_assignment);
         else if ($default_assignment['service'] == 'MailChimp' && $mailchimp_api_key != '')
            $user_subscribed = MWX_SubscribeToMailChimp ($default_assignment, $mailchimp_api_key, $mailchimp_interest_groups);
         else if ($default_assignment['service'] == '1ShoppingCart' && $oneshoppingcart_merchant_id_number != '')
            $user_subscribed = MWX_SubscribeTo1ShoppingCart ($default_assignment, $oneshoppingcart_merchant_id_number);
        }
      else
         {
         MWX__log_event (__FILE__, __LINE__, "NOTE: Not adding user: {$_inputs['payer_email']} to autoresponder {$default_assignment['key']}. Already been added.");
         }
     if ($user_subscribed) update_user_meta ($user_id, 'mwx_user_autoresponded_'.$default_assignment['key'], '1');
      }


}
//===========================================================================

//===========================================================================
function MWX_SubscribeToAweber ($autoresponder_assignment)
{
   global $_inputs;

   // Send special email to add new user to Aweber mailing list.
   MWX__send_email (
   $autoresponder_assignment['list'],  // To
   'memberwingaweber@memberwing.com',  // From
   'Subscribe',
   "New Subscriber (via Wordpress Membership site plugin MemberWing):" .
   "<br />\nSubscriber_First_Name: {$_inputs['first_name']}" .
   "<br />\nSubscriber_Last_Name:  {$_inputs['last_name']}" .
   "<br />\nSubscriber_Email:      {$_inputs['payer_email']}" .
   "<br />\n"
   );

   MWX__log_event (__FILE__, __LINE__, "Note: Aweber subscription initiated for user: {$_inputs['payer_email']}.");
   return true;
}
//===========================================================================

//===========================================================================
function MWX_SubscribeToMailChimp ($autoresponder_assignment, $mailchimp_api_key, $mailchimp_interest_groups)
{
   global $_inputs;

   if (!class_exists('MCAPI'))
      {
      include_once (dirname(__FILE__) . '/MCAPI.class.php');
      }

   // grab an API Key from http://admin.mailchimp.com/account/api/
   $api = new MCAPI($mailchimp_api_key);

   // grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
   // Click the "settings" link for the list - the Unique Id is at the bottom of that page.
   $mwx_settings = MWX__get_settings ();
   $list_id = $mwx_settings ['mailchimp_mail_list_id_number'];

   // Merge variables are the names of all of the fields your mailing list accepts
   // Ex: first name is by default FNAME
   // You can define the names of each merge variable in Lists > click the desired list > list settings > Merge tags for personalization
   // Pass merge values to the API in an array as follows
   $mergeVars = array (
         'FNAME'     => $_inputs['first_name'],
         'LNAME'     => $_inputs['last_name'],
         'USERNAME'  => $_inputs['desired_username'],
         'PASSWORD'  => $_inputs['desired_password'],
         'INTERESTS' => $mailchimp_interest_groups
         );

   if (@$_inputs['MMERGE3'])
   $mergeVars['MMERGE3'] = $_inputs['MMERGE3'];
   if (@$_inputs['MMERGE4'])
   $mergeVars['MMERGE4'] = $_inputs['MMERGE4'];

   if ($api->listSubscribe($autoresponder_assignment['list'], $_inputs['payer_email'], $mergeVars) === true)
      MWX__log_event (__FILE__, __LINE__, "Note: MailChimp subscription for user: {$_inputs['payer_email']} successfully created");
   else
      MWX__log_event (__FILE__, __LINE__, "Warning: MailChimp subscription for user: {$_inputs['payer_email']} failed: api->errorMessage = {$api->errorMessage}");
   return true;
}
//===========================================================================

//===========================================================================
function MWX_SubscribeTo1ShoppingCart ($autoresponder_assignment, $merchant_id) {
   global $_inputs;

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "https://www.mcssl.com/app/contactsave.asp");
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

   $data = array(
   'merchantid' => $merchant_id,
   'ARThankyouURL' => 'www.autowebbusiness.com/app/thankyou.asp?ID='.$merchant_id,
   'copyarresponse' => '1',
   'custom' => '0',
   'defaultar' => $autoresponder_assignment['list'],
   'allowmulti' => '0',
   'visiblefields' => 'Name,Email1',
   'requiredfields' => 'Email1',
   'Name' => $_inputs['first_name'].' '.$_inputs['last_name'],
   'Email1' => $_inputs['payer_email']
   );
   //url-ify the data for the POST
   $fields_string = '';
   foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
   rtrim($fields_string,'&');

   curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
   $output = curl_exec($ch);
   curl_close($ch);
   MWX__log_event (__FILE__, __LINE__, "Note: 1ShoppingCart subscription for user: {$_inputs['payer_email']} successfully sent");
   return true;
}
//===========================================================================

//===========================================================================
// Returns expiry date in format ("2008-04-11 12:43:56 EST") if custom expiry date should be set for this product.
// "" if no custom expiry date required for this product.
function MWX__GetProductExpiryDate ($product_name, $purchase_date)
{
   $mwx_settings = MWX__get_settings ();

   if (!$purchase_date)
      $purchase_date = "now";

   $prods = explode ("\n", trim($mwx_settings['products_lifetimes']));

   foreach ($prods as $prod)
      {
      $p = explode (':', trim($prod));
      if (count($p) == 2)
         {
         if (stristr ($product_name, $p[0]))
            {
            $expires_in_seconds = MWX__ConvertProdTimeToSeconds($p[1]);

            // Matching keyword in custom expiry conditions found
            $expiry_date = date ('Y-m-d H:i:s T', strtotime ("$purchase_date + $expires_in_seconds seconds"));
            return $expiry_date;
            }
         }
      }

   return "";
}
//===========================================================================

//===========================================================================
// Assemble normalized array of product keywords and their delays in seconds: array ('keyword1' => 1234, 'keyword2' => 5678, ...)
function MWX__GetProductsAccessDelays ($mwx_settings=FALSE)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

   $prods = explode ("\n", trim($mwx_settings['products_access_delays']));

   $products_access_delays = array();
   foreach ($prods as $prod)
      {
      $p = explode (':', trim($prod));
      if (count($p) == 2)
         {
         $products_access_delays [$p[0]] = MWX__ConvertProdTimeToSeconds($p[1]);
         }
      }

   return $products_access_delays;
}
//===========================================================================

//===========================================================================
// $text_delay: FALSE, 20m, 24h, 30d
// 0 will yield 0, NULL or false will yield FALSE (value is not specified).
function MWX__ConvertProdTimeToSeconds ($text_delay)
{
   if ($text_delay === 0 || $text_delay === "0")
      return 0;

   if (!$text_delay)
      return FALSE;  // No specified

   if (is_numeric($text_delay))
      return $text_delay*24*60*60;  // 25 - means 25 days

   $strlen_minus_1 = strlen($text_delay) - 1;
   $delay_in = $text_delay [$strlen_minus_1];
   $numeric_delay = substr ($text_delay, 0, $strlen_minus_1);
   switch ($delay_in)
      {
      case 's': $multiplier = 1;          break;   // This is not publicly announced feature.
      case 'm': $multiplier = 60;         break;
      case 'h': $multiplier = 60*60;      break;
      case 'd':
      default:  $multiplier = 24*60*60;   break;
      }

   return $numeric_delay * $multiplier;
}
//===========================================================================


?>