<?php
/*
Plugin Name: MemberWing-X
Plugin URI: http://www.memberwing.com
Version: 8.700
Author: Gleb Esman, http://www.memberwing.com/
Author URI: http://www.memberwing.com/
Description: MemberWing-X Plugin allows you to build powerful multifunctional membership sites with your own integrated affiliate network. MemberWing-X allows selling access to premium content and digital downloadable materials on a one-time or recurring payments basis. MemberWing-X includes secure digital online store builder. MWX Integrated Instant Affiliate Network offers automated affiliate tracking functions as well as instant signup, instant approval and instant payments to your affiliates.
*/

define('MEMBERWING_X_VERSION',  '8.700');
define('MEMBERWING_X_EDITION',  'GA');

/*
---------------------------------------
NOTE: Change Log moved to changelog.txt
---------------------------------------
*/

// Note: Using 'include_once' here will prevent mwx-paypal-x.php to get loaded independently
include (dirname(__FILE__) . '/mwx-include-all.php');

//---------------------------------------------------------------------------
// Insert hooks
register_activation_hook   (__FILE__, 'MWX__activated');
add_action                 ('admin_menu',                'MWX__admin_menu',            222);
add_filter                 ('the_content',               'MWX__the_content_custom_PHP', 222); // Execute custom PHP via custom fields.
add_filter                 ('the_content',               'MWX__the_content',           223);
add_filter                 ('the_content',               'MWX__the_content_digital_online_store', 224); // Processing for [mwx-list-premium-files]
add_filter                 ('the_content_limit',         'MWX__the_content',           224);
add_filter                 ('the_excerpt',               'MWX__the_content',           224);
add_filter                 ('the_content_rss',           'MWX__the_content',           224);
add_filter                 ('the_excerpt_rss',           'MWX__the_content',           224);

// Hiding comments from non-logged on users
add_filter                 ('comment_text',              'MWX__flt_comments',          222);
add_filter                 ('comment_text_rss',          'MWX__flt_comments',          222);
add_filter                 ('comment_excerpt',           'MWX__flt_comments',          222);
add_filter                 ('comments_array',            'MWX__flt_comments_array',    222);
add_filter                 ('comments_number',           'MWX__flt_comments_number',   222);

// Extra products and affiliate data in user profiles
add_action                 ('show_user_profile',         'MWX__show_user_profile');
add_action                 ('edit_user_profile',         'MWX__show_user_profile');
add_action                 ('personal_options_update',   'MWX__update_user_profile');
add_action                 ('edit_user_profile_update',  'MWX__update_user_profile');

add_action                 ('init',                      'MWX__init',                  4);  // Buddupress uses '5' or default. Trying to be first.
add_action                 ('wp_login',                  'MWX__wp_login', 10, 2);
add_action                 ('wp_logout',                 'MWX__wp_logout');

// Custom CSS / header insertion logic
add_action                 ('wp_head',                   'MWX__wp_head',               222);

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_filter                 ("gform_validation", "MWX__gform_validation", 10, 4);

// http://www.gravityhelp.com/documentation/page/Using_the_Gravity_Forms_%22gform_valiation%22_Hook
function MWX__gform_validation ($validation_result)
{
    $form = $validation_result['form'];

    // Pull invitation code and product name submitted in form
    $invitation_code = '';
    $product_name    = '';
    foreach($form['fields'] as &$field)
        {
        if(strpos($field['cssClass'], 'invitation_code') !== false)
            {
            // 'invitation_code' field is found. Get it's value
            // Get submitted value from the $_POST
            $invitation_code = rgpost("input_{$field['id']}");
            }

        if(strpos($field['cssClass'], 'product_name') !== false)
            {
            // 'product_name' field is found. Get it's value
            // Get submitted value from the $_POST
            $product_name = rgpost("input_{$field['id']}");
            }
        }

   // Test value of invitation code here ...
    if (stripos($product_name, 'invitation_code')===0)
        {
        // Product selection was "I have invitation code instead ...", Validate invitation code ...
        $invitation_code_valid = FALSE;
        $invitation_code_error = 'Invitation code is invalid.';

        //----------------------
        // Now check MemberWing-X Invitation codes database and see if this new member is eligible for any product
        // Each Invitation Code is an associative array with the following elements:
        //    'invitation_code'
        //    'total_use_count'
        //    'max_use_count'
        //    'invitation_code_expiry'
        //    'assigned_product'
        //    'product_lifetime_or_expiry'
        //    'referred_by_id'
        //    'active'
        //
        if ($invitation_code)
            {
            $mwx_settings = MWX__get_settings ();

            foreach ((array)@$mwx_settings['invitation_codes'] as $idx=>$mwx_invitation_code)
                {
                if (strtoupper($invitation_code) == strtoupper($mwx_invitation_code['invitation_code']))
                   {
                   // Found posted invitation code in MWX DB
                   $total_use_count = $mwx_settings['invitation_codes'][$idx]['total_use_count'];

                   // Is active?
                    if (!$mwx_invitation_code['active'])
                        {
                        $invitation_code_error = 'This invitation code marked as "inactive" via MWX admin settings.';
                        break;   // This invitation code marked as "inactive" via MWX admin settings.
                        }

                    // Max use count is set and reached?
                    if ($mwx_invitation_code['max_use_count']>0 && ($total_use_count >= $mwx_invitation_code['max_use_count']))
                        {
                        $invitation_code_error = 'Max use count reached';
                        break;   // Max use count reached. No more freebies for new members who use this invitation code.
                        }

                    if ($mwx_invitation_code['invitation_code_expiry'] && (time() > strtotime($mwx_invitation_code['invitation_code_expiry'])))
                        {
                        $invitation_code_error = 'This invitation code is expired';
                        break;   // This invitation code is expired. No more freebies for new members who use this invitation code.
                        }

                    $invitation_code_valid = TRUE;  // Invitation code is valid
                    break;
                    }
                }
            }

        if (!$invitation_code_valid)
            {
            // If validation failed...
            MWX__log_event (__FILE__, __LINE__, "MWX__gform_validation(): Warning: Attempt to use invalid invitation_code: '{$invitation_code}' - {$invitation_code_error}");

            $validation_result['is_valid'] = false;
            $field['failed_validation'] = true;
            $field['validation_message'] = 'Invitation code is invalid.';
            $validation_result['form'] = $form;
            return $validation_result;
            }
        else
            {
            MWX__log_event (__FILE__, __LINE__, "MWX__gform_validation(): Valid invitation_code used: '{$invitation_code}'");
            }
        }

   return $validation_result;
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// http://www.gravityhelp.com/documentation/page/PayPal_Add-on_Developer_Docs
// http://www.gravityhelp.com/documentation/page/Gform_paypal_post_ipn
add_filter("gform_paypal_pre_ipn", "MWX__gform_paypal_pre_ipn", 10, 4);

function MWX__gform_paypal_pre_ipn ($cancel, $paypal_ipn_post_array, $entry, $config)
{
    // Get email address passed from form, not via paypal IPN, because they could be different.
    // Make sure account already created by Gravity Forms in order to proceed with IPN processing.
    if (isset($config['meta']['customer_fields']['email']))
        {
        $email_field_id = $config['meta']['customer_fields']['email'];
        $email_address  = @$entry[$email_field_id];
        }
    else
        $email_address  = false;

    if (!$email_address || !email_exists ($email_address))
        {
        MWX__log_event (__FILE__, __LINE__, "MWX__gform_paypal_pre_ipn () called. Note: no email present or no account with this email address: '{$email_address}' exists yet. Saving paypal IPN data in cache. Skipping processing for now.");

        // MWX__remove_new_user_data_from_cache ($new_user_email, $keep_in_cache=FALSE)
         if ($email_address)
            {
            $paypal_ipn_post_array['payer_email'] = $email_address;
            MWX__save_new_user_data_in_cache ($email_address, array ('paypal_ipn' => $paypal_ipn_post_array, 'entry' => $entry));
            }
         else
            {
            MWX__log_event (__FILE__, __LINE__, "WARNING: Cannot detect email address");
            }

        return FALSE;   // Means continue processing
        }
    else
        MWX__log_event (__FILE__, __LINE__, "MWX__gform_paypal_pre_ipn () called. Email+account already exists. Processing ...");

    // User account already created by Gravity Forms. Proceeding ...

    MWX__process_third_party_paypal_ipn ($paypal_ipn_post_array, $entry);

    return FALSE; // Means continue processing
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function MWX__process_third_party_paypal_ipn ($paypal_ipn_post_array, $extra_data)
{
    $mwx_settings = MWX__get_settings ();

    // Create global '$_inputs' array and initialize it with default values
    global $_inputs;
    $_inputs = array ();
    MWX__ResetInputs ($_inputs);

    //---------------------------------------
    // Sanitize posted variables into '$_inputs' array
    //
    $_inputs['item_name']         = isset($paypal_ipn_post_array['item_name'])?$paypal_ipn_post_array['item_name']:(isset($paypal_ipn_post_array['item_name1'])?$paypal_ipn_post_array['item_name1']:(isset($paypal_ipn_post_array['memo'])?$paypal_ipn_post_array['memo']:(@$paypal_ipn_post_array['product_name']?$paypal_ipn_post_array['product_name']:"Unknown Item Name")));
       $_inputs['item_name']         = str_replace ("'", "", $_inputs['item_name']);
    $_inputs['first_name']        = isset($paypal_ipn_post_array['first_name'])?$paypal_ipn_post_array['first_name']:"";
    $_inputs['last_name']         = isset($paypal_ipn_post_array['last_name'])?$paypal_ipn_post_array['last_name']:"";
    $_inputs['payer_email']       = @$paypal_ipn_post_array['payer_email'];
    $_inputs['payer_id']          = isset($paypal_ipn_post_array['payer_id'])?$paypal_ipn_post_array['payer_id']:"";
    $_inputs['subscr_id']         = isset($paypal_ipn_post_array['subscr_id'])?$paypal_ipn_post_array['subscr_id']:'0';   // Only present for subscriptions. Unique for every act of subscription, regarldess of payer.
    $_inputs['subscr_date']       = isset($paypal_ipn_post_array['subscr_date'])?$paypal_ipn_post_array['subscr_date']:"now";
       $_inputs['U_txn_date']     = date ('Y-m-d H:i:s T', strtotime (urldecode($_inputs['subscr_date']))); // Normalize it for database usage.
    $_inputs['recurring']         = isset($paypal_ipn_post_array['recurring'])?$paypal_ipn_post_array['recurring']:NULL;
    $_inputs['period3']           = isset($paypal_ipn_post_array['period3'])?$paypal_ipn_post_array['period3']:NULL;
    $_inputs['payment_status']    = isset($paypal_ipn_post_array['payment_status'])?strtolower($paypal_ipn_post_array['payment_status']):"unknown";
    $_inputs['mc_amount3_gross']  = isset($paypal_ipn_post_array['mc_gross'])?$paypal_ipn_post_array['mc_gross']:(isset($paypal_ipn_post_array['mc_amount3'])?$paypal_ipn_post_array['mc_amount3']:(isset($paypal_ipn_post_array['mc_amount2'])?$paypal_ipn_post_array['mc_amount2']:(isset($paypal_ipn_post_array['mc_amount1'])?$paypal_ipn_post_array['mc_amount1']:(@$paypal_ipn_post_array['payment_gross']?$paypal_ipn_post_array['payment_gross']:"0")))); // mc_gross is set for 1st notif., mc_amount3 for 2nd.

    $_inputs['mc_currency']       = isset($paypal_ipn_post_array['mc_currency'])?$paypal_ipn_post_array['mc_currency']:"";
    $_inputs['txn_id']            = isset($paypal_ipn_post_array['txn_id'])?$paypal_ipn_post_array['txn_id']:"";
    $_inputs['parent_txn_id']     = isset($paypal_ipn_post_array['parent_txn_id'])?$paypal_ipn_post_array['parent_txn_id']:"";
    $_inputs['txn_type']          = isset($paypal_ipn_post_array['txn_type'])?$paypal_ipn_post_array['txn_type']:""; // When payment_status = 'Refunded', 'txn_type' is not set.
       if ($_inputs['payment_status'] == 'refunded' || $_inputs['payment_status'] == 'reversed')
          $_inputs['txn_type'] = "refund";
    $_inputs['receiver_email']    = isset($paypal_ipn_post_array['receiver_email'])?$paypal_ipn_post_array['receiver_email']:"";
    $_inputs['customer_ip']       = @$extra_data['ip'];
    $_inputs['referred_by_id']    = "self";       ///!!! Affiliate detection for Gravity Forms-based registration is not supported yet
    $_inputs['aff_paid']          = FALSE;        // Affiliate is paid via instant Adaptive Chained Payment Method.
    $_inputs['aff_refunded']      = FALSE;    // Refund was taken from Affiliate via Adaptive Refund Method. Might be possible if: Original purchase was Adaptive/Chained + Adaptive refunding was used + Affiliate has API agreement with seller.
    if ((isset($mwx_settings['paypal_sandbox_enabled']) && $mwx_settings['paypal_sandbox_enabled']) || $_SERVER['REMOTE_ADDR']=='216.113.191.33')
       $_inputs['is_sandbox'] = TRUE;
    else
       $_inputs['is_sandbox'] = FALSE;
    //---------------------------------------

    MWX__log_event (__FILE__, __LINE__, "Note: Processing IPN data sent by third party (Gravity Forms?) ...");
    MWX__TransactionTypeSwitch ();
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_action                 ("gform_user_registered",     "MWX__gform_user_registered",       10, 3);

// We hook it to do invitation code processing
function MWX__gform_user_registered ($user_id, $config, $entry)
{
   // Gravity Forms registration succeeded. Check if Invitation Code is present...

   // $user_info = get_userdata ($user_id);
   // $user_info->ID - already registered user ID.
   // $user_info->product_name  - will be name of product selected. This is User Meta added via Forms->User Registration->User Meta
   MWX__user_register ($user_id, @$entry['ip'], TRUE);   // Only process invitation code part, skip autoresponder part.

}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// New user registration - integration with autoresponders.
add_action                 ('user_register',             'MWX__user_register',         222, 3);

function MWX__user_register ($user_id, $registration_ip=FALSE, $process_invitation_code_only=FALSE)
{
   // At this point no custom metadata fields (such as 'invitation_code') is present.

   $user_info = get_userdata ($user_id);

   // See if there is cached data saved for this user before it was registered (sent by Paypal IPN).
   // If yes - process it (add new product for this user once it is already registered here)
   $new_user_email   = $user_info->user_email;
   $new_user_data    = MWX__remove_new_user_data_from_cache ($new_user_email);

   if (is_array($new_user_data) && isset($new_user_data['paypal_ipn']))
      {
      MWX__log_event (__FILE__, __LINE__, "NOTE: Detected cached IPN data for user: '$new_user_email'. Processing. Cached Paypal IPN data:\n" . serialize($new_user_data['paypal_ipn']));
      $extra_data = @$new_user_data['entry'];
      MWX__process_third_party_paypal_ipn ($new_user_data['paypal_ipn'], $extra_data);
      }

   //---------------------------------------
   //----------------------
   // At this point user is already registered. Add invitation code, if present.
   // Invitation code functionality could be added via Register Plus Redux plugin:
   // http://wordpress.org/extend/plugins/register-plus-redux/
   // NOTE: Register Plus Redux plugin is no longer supported. Use Gravity Forms plugin, Developers edition: http://toprate.org/gf

   // Redux haven't updated usermeta at this point yet. But we can pull invitation code from POST directly.
   // $invitation_code = get_usermeta ($user_id, 'invitation_code');
   $invitation_code = @$_POST['invitation_code'];   // Pull it if user registered via Register Plus Redux.

   // Also try to pull invitation code from Gravity Forms. For that to work:
   // -  Gravity Forms developer license is required
   // -  Gravity User Registration addon (plugin) needs to be installed
   // -  Admin->Forms->User Registration->"form name"->User Meta, add custom meta key: "invitation_code" and Map it to form's "Invitation Code" field.
   if (!$invitation_code && isset($user_info->invitation_code))
      $invitation_code = $user_info->invitation_code;   // Pull it if user registered via Gravity Forms.

   $mwx_extra_user_data = MWX__get_usermeta_array ($user_id, 'mwx_extra_user_data');
   if (!$mwx_extra_user_data || !is_array($mwx_extra_user_data))
      $mwx_extra_user_data = array();
   $mwx_extra_user_data['invitation_code']         = $invitation_code;
   $mwx_extra_user_data['invitation_code_status']  = 'Undefined in MWX settings';
   $mwx_extra_user_data['registration_date']       = date ("Y-m-d H:i:s T", strtotime("now"));
   $mwx_extra_user_data['registration_ip_addr']    = $registration_ip?$registration_ip:@$_SERVER['REMOTE_ADDR'];
   $mwx_extra_user_data['referred_by_id']          = ""; // This might be filled a bit later on if that user used proper invitation code... See below...
   //----------------------

   $mwx_settings = MWX__get_settings ();

   //----------------------
   // Now check MemberWing-X Invitation codes database and see if this new member is eligible for any product
   // Each Invitation Code is an associative array with the following elements:
   //    'invitation_code'
   //    'total_use_count'
   //    'max_use_count'
   //    'invitation_code_expiry'
   //    'assigned_product'
   //    'product_lifetime_or_expiry'
   //    'referred_by_id'
   //    'active'
   //
   if ($invitation_code)
      {
      foreach ((array)@$mwx_settings['invitation_codes'] as $idx=>$mwx_invitation_code)
         {
         if (strtoupper($invitation_code) == strtoupper($mwx_invitation_code['invitation_code']))
            {
            $mwx_settings['invitation_codes'][$idx]['total_use_count']++;
            $total_use_count = $mwx_settings['invitation_codes'][$idx]['total_use_count'];
            MWX__update_settings ($mwx_settings);

            // Is active?
            if (!$mwx_invitation_code['active'])
               {
               MWX__log_event (__FILE__, __LINE__, "NOTE: New user: '{$user_info->user_login}' tried to use Invitation Code: '$invitation_code' that is marked as inactive. NOT adding any product for this user...");
               $mwx_extra_user_data['invitation_code_status']  = 'invalid, deactivated';
               break;   // This invitation code marked as "inactive" via MWX admin settings.
               }

            // Max use count is set and reached?
            if ($mwx_invitation_code['max_use_count']>0 && ($total_use_count > $mwx_invitation_code['max_use_count']))
               {
               MWX__log_event (__FILE__, __LINE__, "NOTE: New user: '{$user_info->user_login}' tried to use Invitation Code: '$invitation_code' for which maximum use count is reached. Use count/Max allowed = $total_use_count/{$mwx_invitation_code['max_use_count']}. NOT adding any product for this user...");
               $mwx_extra_user_data['invitation_code_status']  = 'invalid, use count exceeded';
               break;   // Max use count reached. No more freebies for new members who use this invitation code.
               }

            if ($mwx_invitation_code['invitation_code_expiry'] && (strtotime($mwx_extra_user_data['registration_date']) > strtotime($mwx_invitation_code['invitation_code_expiry'])))
               {
               MWX__log_event (__FILE__, __LINE__, "NOTE: New user: '{$user_info->user_login}' tried to use expired Invitation Code: '$invitation_code'. NOT adding any product for this user...");
               $mwx_extra_user_data['invitation_code_status']  = 'invalid, expired';
               break;   // This invitation code is expired. No more freebies for new members who use this invitation code.
               }

            $mwx_extra_user_data['invitation_code_status']  = 'valid';
            update_user_meta ($user_id, 'mwx_extra_user_data', serialize ($mwx_extra_user_data));

            //-------------
            // See if affiliate referred ID was specified with this invitation code.
            if ($mwx_invitation_code['referred_by_id'])
               {
               // Set referred_by_id into mwx_extra_user_data
               $mwx_extra_user_data['referred_by_id'] = $mwx_invitation_code['referred_by_id'];
               }
            //-------------

            //-------------
            // Here we have new member who used valid and active invitation code. Auto-add him the product that was specified with this invitation code
            if ($mwx_invitation_code['assigned_product'])
               {
               $products_purchased = MWX__GetListOfProductsForUser ($user_id);
               if (!is_array($products_purchased))
                  $products_purchased = array();

               $product_expiry_date = $mwx_invitation_code['product_lifetime_or_expiry']?date ("Y-m-d H:i:s T", strtotime($mwx_invitation_code['product_lifetime_or_expiry'])):"";
               $products_purchased[] =
                  array (
                     'product_id'      => "",
                     'product_name'    => $mwx_invitation_code['assigned_product'],
                     'purchase_date'   => $mwx_extra_user_data['registration_date'],
                     'expiry_date'     => $product_expiry_date,
                     'txn_ids'         => array(),
                     'subscr_id'       => $invitation_code,
                     'referred_by_id'  => $mwx_invitation_code['referred_by_id'],
                     'product_status'  => 'active',      // 'active'(customer is in good standing), 'cancelled'(subscription), 'ended'(subscription ended normally), 'expired'(forced expiry date reached), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
                     );

               update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
               MWX__log_event (__FILE__, __LINE__, "NOTE: Auto-added product: '{$mwx_invitation_code['assigned_product']}' with expiration date: '$product_expiry_date' for new user: '{$user_info->user_login}' who used Invitation Code: '$invitation_code' to register.");
               }
            else
               {
               MWX__log_event (__FILE__, __LINE__, "NOTE: New user: '{$user_info->user_login}' used Invitation Code: '$invitation_code' but no product was specified with that invitation code.");
               }
            //-------------

            break;
            }
         }
      }
   //----------------------

   update_user_meta ($user_id, 'mwx_extra_user_data', serialize ($mwx_extra_user_data));


   //---------------------------------------

   // Purchase calls MWX__AddUserToAutoresponder() by itself. Testing '$in_process_purchase' variable prevents calling it second time when this hook is triggered.
   global $in_process_purchase;
   if (!isset($in_process_purchase) && !$process_invitation_code_only)
      {
      MWX__AddUserToAutoresponder ($user_id, $mwx_settings);
      }
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// 404 handling - no 'join' page reminder - in case webmaster forgot to create one
add_action('template_redirect', 'MWX__template_redirect');

function MWX__template_redirect()
{
if (is_404() && preg_match ('@subscribe/?$@', (string)@$_SERVER['REQUEST_URI']))
   {
   include (dirname(__FILE__) . '/subscribe-page-404.php');
   exit;
   }
}

//-------------------------------------------------------------
// Fix hiding comments for non-logged on users for Discus commenting system
add_filter                 ('the_posts', 'MWX__the_posts');
function MWX__the_posts ($posts)
{
   if (MWX__do_hide_comments())
      {
      if (isset($posts[0]->comment_status))
         $posts[0]->comment_status = 'closed';
      }
   return ($posts);
}
//-------------------------------------------------------------

//-------------------------------------------------------------
add_action ('admin_head', 'MWX__admin_head');
function MWX__admin_head ()
{
   global $g_MWX__plugin_directory_url;

   $mwx_admin_js_url  = $g_MWX__plugin_directory_url . '/js/admin/mwx-admin.js';
   echo '<script type="text/javascript" src="' . $mwx_admin_js_url .'"></script>';

   $mwx_admin_css_url = $g_MWX__plugin_directory_url . '/css/admin/mwx-admin.css';
   echo '<link rel="stylesheet" type="text/css" href="' . $mwx_admin_css_url . '" />';
}
//-------------------------------------------------------------

//-------------------------------------------------------------
add_filter( 'plugin_row_meta', 'MWX__plugin_row_meta', 10, 2 );
function MWX__plugin_row_meta ($links, $file)
{
   $plugin = plugin_basename(__FILE__);
   if ($file != $plugin)
      return $links;

   $mwx_settings = MWX__get_settings ();
   if (
         (
         $mwx_settings['memberwing-x-license-info']['license_status'] != 'valid'       ||
         $mwx_settings['memberwing-x-license-info']['license_substatus'] != 'allowed'  ||
         $mwx_settings['memberwing-x-license-info']['is_sponsored']
         )
      &&
      $mwx_settings['memberwing-x-license-info']['message']
      )
      {
      if ($file == $plugin)
         {
         // create link
         $license_status = "<b>MemberWing-X License Code status: " . $mwx_settings['memberwing-x-license-info']['license_status'] . " (" . $mwx_settings['memberwing-x-license-info']['license_substatus'] . ")</b>" . '<br />';
         return array_merge ($links, array ('<div align="center" style="border:1px solid red;background-color:#FF0;padding:2px 4px;">' . $license_status . $mwx_settings['memberwing-x-license-info']['message'] . '</div>'));
         }
      }
   else
      return $links;
}
//-------------------------------------------------------------

add_shortcode('mwx_auto_register_aweber', 'MWX__auto_registration_aweber_shortcode');

//---------------------------------------------------------------------------

//===========================================================================
function MWX__wp_head ()
{
   $mwx_settings = MWX__get_settings ();
   $current_template = $mwx_settings['dos_current_active_template']; // Default: 't1'. But this value is == last edited template in admin screen. Allows admin screen persistance

   // Force load stylesheet and .js for 't1' default template.
?>
<link rel="stylesheet" type="text/css" href="<?php echo $mwx_settings['dos_templates']['t1']['dos_style_stylesheet_file']; ?>" />
<script type="text/javascript" src="<?php echo $mwx_settings['dos_templates']['t1']['dos_style_javascript_file']; ?>"></script>
<?php

   if ($current_template != 't1')
      {
      // Load stylesheet for currently active template as well
      if (@$mwx_settings['dos_templates'][$current_template]['dos_style_stylesheet_file'])
         {
?>
<link rel="stylesheet" type="text/css" href="<?php echo $mwx_settings['dos_templates'][$current_template]['dos_style_stylesheet_file']; ?>" />
<?php
         }

      // Load .js for currently active template
      if (@$mwx_settings['dos_templates'][$current_template]['dos_style_javascript_file'])
         {
?>
<script type="text/javascript" src="<?php echo $mwx_settings['dos_templates'][$current_template]['dos_style_javascript_file']; ?>"></script>
<?php
         }
      }

}
//===========================================================================

//===========================================================================
function MWX__init ()
{
	if (!session_id()) // necessary for 'current_ip_info' functions to work - ability to save login IP address that may be different from subsequent IP's used by the same browser.
  	session_start();

  if (!isset($_SESSION['login_ip']))
  {
  	$_SESSION['login_ip'] = MWX__get_visitor_REMOTE_ADDR();		// 'user_ip_data' support
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__init(): Saving in SESSION new login IP: " . $_SESSION['login_ip'] . ", Browser: " . $_SERVER['HTTP_USER_AGENT']);
//////////////////////!!!
  }

   // Process user's cookie and initialize current refering affiliate ID (if present).
   MWX__SetCurrentAffiliateRawID (MWX__SetCookie ());

   // Make sure jQuery is loaded.
   wp_enqueue_script ('jquery');

	if (file_exists(dirname(__FILE__) . '/ext-mwx-init.php'))
		include_once (dirname(__FILE__) . '/ext-mwx-init.php');
}
//===========================================================================

//===========================================================================
// 'user_ip_data' support
function MWX__wp_logout ()
{
	if (session_id ())
		session_destroy ();
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__wp_logout(): session destroyed");
//////////////////////!!!
}
//===========================================================================

//===========================================================================
// On each login register 'user_ip_data'['registered']
function MWX__wp_login ($user_login, $wp_user)
{
	if (session_id ())
		session_destroy ();	// As per: http://silvermapleweb.com/using-the-php-session-in-wordpress/

  $mwx_settings = MWX__get_settings ();
  if (!@$mwx_settings['ip_protection_max_allowed_addresses'])
  	$mwx_settings['ip_protection_max_allowed_addresses'] = 3;
  if (!@$mwx_settings['ip_protection_cidr_mask'])
  	$mwx_settings['ip_protection_cidr_mask'] = 24;
  if (!@$mwx_settings['ip_protection_log_history_size'])
  	$mwx_settings['ip_protection_log_history_size'] = 25;

	$current_ip_info = array(
		'datetimestamp' 		=> date ('Y-m-d H:i:s T', strtotime ("now")),
		'REMOTE_ADDR'   		=> MWX__get_visitor_REMOTE_ADDR (),
		'HTTP_USER_AGENT'		=> $_SERVER['HTTP_USER_AGENT'],
		'count'							=> 1
		);

//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__wp_login($user_login): Registered login event from '{$current_ip_info['REMOTE_ADDR']}', Browser: " . $_SERVER['HTTP_USER_AGENT'], serialize($current_ip_info));
//////////////////////!!!

	$user_ip_data = MWX__get_usermeta_array ($wp_user->ID, 'user_ip_data');

	// Stack of last 25 logged-on-from hits
	if (!isset($user_ip_data['log_history']) || !is_array($user_ip_data['log_history']))
		$user_ip_data['log_history'] = array();

	// Officially registered IP addresses from which user is loging in from.
	// No more than 'ip_protection_max_allowed_addresses' will be stored in there
	if (!isset($user_ip_data['registered_ips']) || !is_array($user_ip_data['registered_ips']))
		$user_ip_data['registered_ips'] = array();

	//------------------------
	// Save hit into 'log_history', making sure there aren't too many elements already in, popping out the olderst one if it is.
	while (count($user_ip_data['log_history']) >= $mwx_settings['ip_protection_log_history_size'])
	{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "Note: too many entries within 'log_history' already (" . count($user_ip_data['log_history']) . "), purging the oldest one");
//////////////////////!!!
		array_shift($user_ip_data['log_history']);
	}

	$user_ip_data['log_history'][] = $current_ip_info;
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "'log_history' contains '" . count($user_ip_data['log_history']) . "' entries so far.");
//////////////////////!!!
	//------------------------

	//------------------------
	// 1. Remove possible duplicates according to current state of 'ip_protection_cidr_mask'
	// 2. Check 'registered_ips' for match, and if no match found (according to current state of 'ip_protection_cidr_mask') - add new entry into it

	// 1.

	if ($mwx_settings['ip_protection_enabled'])
	{
		$registered_ips_modified = false;
		// Force-add new entry into array and then re-check the whole array for duplicates.
		$user_ip_data['registered_ips'][] = $current_ip_info;
		$index_of_last_element = count($user_ip_data['registered_ips']) - 1;
		foreach ($user_ip_data['registered_ips'] as $idx => $ip_info)
		{
			if ($idx == 0) continue;
			for ($i = 0; $i < $idx; $i++)
			{
				if (MWX__ip_addresses_matching ($user_ip_data['registered_ips'][$i]['REMOTE_ADDR'], $user_ip_data['registered_ips'][$idx]['REMOTE_ADDR'], $mwx_settings['ip_protection_cidr_mask']))
				{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "Note: removing duplicate(index=$idx) '" . $user_ip_data['registered_ips'][$idx]['REMOTE_ADDR'] . "' of already existing entry: '" . $user_ip_data['registered_ips'][$i]['REMOTE_ADDR'] . "' (saved et: " . $user_ip_data['registered_ips'][$i]['datetimestamp'] . ")");
//////////////////////!!!
					unset($user_ip_data['registered_ips'][$idx]);

					if ($idx == $index_of_last_element)
					{
						$user_ip_data['registered_ips'][$i]['count'] ++;	// Increment "times used to login" counter for some pre-existing entry.
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "NOTE: Incrementing usage counter for element: '" . $user_ip_data['registered_ips'][$i]['REMOTE_ADDR'] . "'. New counter value: " . $user_ip_data['registered_ips'][$i]['count']);
//////////////////////!!!
					}

					$registered_ips_modified = true;
					break;
				}
			}
		}

		if ($registered_ips_modified)
			// Resort: make indexes sequential
			$user_ip_data['registered_ips'] = array_values($user_ip_data['registered_ips']);

		// Make sure number of elements in array is equals to '$mwx_settings['ip_protection_max_allowed_addresses']'
		while (count($user_ip_data['registered_ips']) > ($mwx_settings['ip_protection_max_allowed_addresses']))
		{
			// Remove last added element from array
			$popped_out_element = array_pop ($user_ip_data['registered_ips']);
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "NOTE: ejecting most recent entry into 'registered_ips'. Too many entries already. Popped out element: ", serialize($popped_out_element));
//////////////////////!!!
		}
	}
	else
	{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "NOTE: mwx_settings['ip_protection_enabled'] is FALSE. not touching 'registered_ips' array.");
//////////////////////!!!
	}
	//------------------------

//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "NOTE: saving final user_ip_data for user '$user_login'", serialize($user_ip_data));
//////////////////////!!!
   // Save updates
	update_user_meta ($wp_user->ID, 'user_ip_data', serialize ($user_ip_data));
}
//===========================================================================

//===========================================================================
// Initial activation code here such as: DB tables creation, storing initial settings.

function MWX__activated ()
{
   global $wp_rewrite;
   $wp_rewrite->flush_rules();

   global   $g_MWX__config_defaults;
   // Initial set/update default options

   $mwx_default_options = $g_MWX__config_defaults;

   // This will overwrite default options with already existing options but leave new options (in case of upgrading to new version) untouched.
   $mwx_settings = MWX__get_settings ();
   if (is_array ($mwx_settings))
      {
      foreach ($mwx_settings as $key=>$value)
         $mwx_default_options[$key] = $value;
      }

   //------------------------------------------------------------------------
   // Renamed/modified Settings migration

   // Update premium content warning to support TSI edition.
   // Older version did not contain {ACCESS_DELAYED_MSG} tag inside 'premium_content_warning'.
   // If that is the case - inject it.
   if (strpos ($mwx_default_options['premium_content_warning'], '{ACCESS_DELAYED_MSG}') === FALSE)
      {
      $mwx_default_options['premium_content_warning'] = str_replace ('{PROMO_MSG}', '{PROMO_MSG} {ACCESS_DELAYED_MSG}', $mwx_default_options['premium_content_warning']);
      }

   if (@$mwx_settings['private_directory_names'])
      {
      $mwx_default_options['individual_access_directory_names'] = $mwx_settings['private_directory_names'];
      unset ($mwx_default_options['private_directory_names']);
      }
   if (@$mwx_settings['keyworded_directory_names'])
      {
      $mwx_default_options['group_access_directory_names'] = $mwx_settings['keyworded_directory_names'];
      unset ($mwx_default_options['keyworded_directory_names']);
      }

   // DW
   if (@$mwx_settings['aweber_integration_enabled'] == '1')
      {
        MWX__log_event (__FILE__, __LINE__, "Updating aweber autoresponder settings");
        // create new setting for this list
       $new_autoresponder_level = 'default';
       $new_autoresponder_list = $mwx_settings['aweber_list_email'];
       $new_autoresponder_service = "Aweber";
         // create key for this assignment
         $assignment_key = md5($new_autoresponder_level.$new_autoresponder_list.$new_autoresponder_service);
         // build array
         $assignment = array("level"   => $new_autoresponder_level,
                             "list"    => $new_autoresponder_list,
                             "service" => $new_autoresponder_service,
                             "key"     => $assignment_key);
        // append to existing array
        if (!isset($mwx_settings['autoresponder_assignments'])) $mwx_settings['autoresponder_assignments'] = array();
        array_push ($mwx_settings['autoresponder_assignments'], $assignment);
       $mwx_default_options['autoresponder_assignments'] = $mwx_settings['autoresponder_assignments'];
       // unset old option
         unset ($mwx_default_options['aweber_integration_enabled']);
         unset ($mwx_default_options['aweber_list_email']);
     }
   if (@$mwx_settings['mailchimp_integration_enabled'] == '1')
      {
        MWX__log_event (__FILE__, __LINE__, "Updating mailchimp autoresponder settings");
        // create new setting for this list
       $new_autoresponder_level = 'default';
       $new_autoresponder_list = $mwx_settings['mailchimp_mail_list_id_number'];
       $new_autoresponder_service = "MailChimp";
         // create key for this assignment
         $assignment_key = md5($new_autoresponder_level.$new_autoresponder_list.$new_autoresponder_service);
         // build array
         $assignment = array("level"   => $new_autoresponder_level,
                             "list"    => $new_autoresponder_list,
                             "service" => $new_autoresponder_service,
                             "key"     => $assignment_key);
        // append to existing array
        if (!isset($mwx_settings['autoresponder_assignments'])) $mwx_settings['autoresponder_assignments'] = array();
        array_push ($mwx_settings['autoresponder_assignments'], $assignment);
       $mwx_default_options['autoresponder_assignments'] = $mwx_settings['autoresponder_assignments'];
       // unset old option
         unset ($mwx_default_options['mailchimp_integration_enabled']);
         unset ($mwx_default_options['mailchimp_mail_list_id_number']);
     }
   // DW/
   //------------------------------------------------------------------------

   update_option ('MemberWing-X', $mwx_default_options);
   MWX__Validate_License ($mwx_default_options['memberwing-x-license_code']);

   // Re-get anew settings.
   $mwx_settings = MWX__get_settings ();
   MWX__DOS_create_database_tables ($mwx_settings);
}
//===========================================================================

//===========================================================================
function MWX__admin_menu ()
{
//  add_options_page ('MemberWing-X plugin', 'MemberWing-X plugin', 'administrator', 'MemberWing-X plugin', 'MWX__render_settings_page');

   add_menu_page    (
      'MemberWingX General Settings',           // Page Title
      '<div align="center" style="font-size:90%;"><span style="font-weight:bold;">M</span>ember<span style="font-weight:bold;">W</span>ing<span style="color:red;font-weight:bold;">X</span></div>',              // Menu Title - lower corner of admin menu
      'administrator',                          // Capability
      'memberwing-x-settings',                  // handle
      'MWX__render_general_settings_page',      // Function
      get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/mwx-admin-icon3.png", __FILE__)          // Icon URL
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWingX General Settings',           // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;General Settings',                       // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings',                  // Handle - First submenu's handle must be equal to parent's handle to avoid duplicate menu entry.
      'MWX__render_general_settings_page'       // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Digital Content Protection', // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Digital Content Protection',             // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-dcp',              // Handle
      'MWX__render_dcp_settings_page'           // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Digital Online Store Builder', // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Digital Online Store Builder',           // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-dos',              // Handle
      'MWX__render_dos_settings_page'           // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Categories Settings',        // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Categories Settings',                      // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-categories',         // Handle
      'MWX__render_categories_settings_page'      // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Products and Time Settings',        // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Products and Time Settings',                      // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-products',         // Handle
      'MWX__render_products_settings_page'      // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Invitation Codes',        // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Invitation Codes',                      // Menu Title
      'administrator',                             // Capability
      'memberwing-x-settings-invitation-codes',    // Handle
      'MWX__render_invitation_codes_settings_page' // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Paypal Settings',           // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Paypal Settings',                        // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-paypal',           // Handle
      'MWX__render_paypal_settings_page'        // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Integrated Affiliate System Settings', // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Affiliate Network Settings',             // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-affiliate',        // Handle
      'MWX__render_affiliate_settings_page'     // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Affiliate payouts',        // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Affiliate Stats and Payouts',                      // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-affiliate-payouts',// Handle
      'MWX__render_affiliate_payouts_page'      // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Autoresponders Settings',  // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Autoresponders',                         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-autoresponders',   // Handle
      'MWX__render_autoresponders_settings_page'// Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Email Settings',           // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Email Settings',                         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-email',            // Handle
      'MWX__render_email_settings_page'// Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: User Management',           // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;User Management',                         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-user-management',            // Handle
      'MWX__render_user_management_settings_page'// Function
      );
   add_submenu_page (
      'memberwing-x-settings-user-management',                  // Parent
      'MemberWing-X: User Profile',           // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;User Profile',                         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-user-profile',            // Handle
      'MWX__render_user_settings_page'// Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Integration with Other Systems, Services and Software',  // Page Title
      '<span style="font-weight:bold;color:#ff3c00;">&bull;</span>&nbsp;Integration with other systems',         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-other-systems',   // Handle
      'MWX__render_other_systems_settings_page'// Function
      );
}

function MWX__render_general_settings_page ()         { MWX__render_settings_page   ('general'); }
function MWX__render_dcp_settings_page ()             { MWX__render_settings_page   ('dcp'); }
function MWX__render_dos_settings_page ()             { MWX__render_settings_page   ('dos'); }
function MWX__render_categories_settings_page ()      { MWX__render_settings_page   ('categories'); }
function MWX__render_products_settings_page ()        { MWX__render_settings_page   ('products'); }
function MWX__render_invitation_codes_settings_page (){ MWX__render_settings_page   ('invitation codes'); }
function MWX__render_paypal_settings_page ()          { MWX__render_settings_page   ('paypal'); }
function MWX__render_autoresponders_settings_page ()  { MWX__render_settings_page   ('autoresponders'); }
function MWX__render_email_settings_page ()           { MWX__render_settings_page   ('email'); }
function MWX__render_affiliate_settings_page ()       { MWX__render_settings_page   ('affiliate settings'); }
function MWX__render_affiliate_payouts_page ()        { MWX__render_settings_page   ('affiliate payouts'); }
function MWX__render_user_management_settings_page () { MWX__render_settings_page   ('user management'); }
function MWX__render_user_settings_page ()            { MWX__render_settings_page   ('user profile'); }
function MWX__render_other_systems_settings_page ()   { MWX__render_settings_page   ('other systems'); }

// Do admin panel business, assemble and output admin page HTML
function MWX__render_settings_page ($menu_page_name)
{
   if (isset ($_POST['button_update_mwx_settings']))
      {
      MWX__update_settings ();
echo <<<HHHH
<div align="center" style="background-color:#FFA;padding:5px;font-size:120%;border: 1px solid gray;margin:5px;">
Settings updated!
</div>
HHHH;
      }
   else if (isset($_POST['button_reset_mwx_settings']))
      {
      MWX__reset_all_settings ();
echo <<<HHHH
<div align="center" style="background-color:#FFA;padding:5px;font-size:120%;border: 1px solid gray;margin:5px;">
All settings reverted to all defaults
</div>
HHHH;
      }
   else if (isset($_POST['button_reset_partial_mwx_settings']))
      {
      MWX__reset_partial_settings ();
echo <<<HHHH
<div align="center" style="background-color:#FFA;padding:5px;font-size:120%;border: 1px solid gray;margin:5px;">
Settings on this page reverted to defaults
</div>
HHHH;
      }
   else if (isset($_POST['validate_memberwing-x-license']))
      {
//      $mwx_settings = MWX__get_settings ();
//      $mwx_settings['memberwing-x-license_code'] = $_POST['memberwing-x-license_code'];
      MWX__update_settings ();
      }

   // Output full admin settings HTML
   MWX__render_admin_page_html ($menu_page_name);
}
//===========================================================================

//===========================================================================
//
// filter for posts, pages, excerpts and rss feed contents.

function MWX__the_content ($content = '')
{
   global $id;                      // ID of current article/post/page
   global $current_user;            // currently logged-on user
   $user_id = $current_user->ID;    // All user's data: $current_user_data = get_userdata ($current_user->ID);

   $mwx_settings = MWX__get_settings ();

   // Automatic Integration Code Insertion for Paypal Buttons
   if ($mwx_settings['paypal_integration_code_auto_insert'])
      $content = preg_replace_callback ('@<form[^>]+action=[\'\"]https://www\.(sandbox\.)?paypal\.com.+?</form>@si', 'MWX__preg_replace_callback_Paypal', $content);

   // Automatic Integration Code Insertion for NMI Payments Buttons
   if ($mwx_settings['nmi_integration_enabled'])
      $content = preg_replace_callback ('@<form[^>]+action=[\'\"]https://secure\.nmi\.com.+?</form>@si', 'MWX__preg_replace_callback_NMI', $content);

   if ($mwx_settings['mwx_affiliate_network_enabled'])
      {
      $custom_data = MWX__PackCustomData ($mwx_settings['secret_password'], MWX__GetCurrentAffiliateRawID());
      $content = preg_replace ('/MWX_AFFILIATE_TRACKING_DATA/', $custom_data, $content);

      // ECWID integration. See also: http://kb.ecwid.com/Affiliates%20features
      if ($mwx_settings['ecwid_affiliate_integration_enabled'])
         {
         $ecwid_search_string          = '<script type="text/javascript"> xProductBrowser';
         $ecwid_search_string_regex    = '|' . '\<script\s+type=\"text\/javascript"\>\s+xProductBrowser' . '|i';

         $curr_aff_raw_id   = MWX__GetCurrentAffiliateRawID    ();
         $curr_aff_username = MWX__GetCurrentAffiliateUsername ($curr_aff_raw_id);
         if ($mwx_settings['ecwid_affiliate_tracking_detailed'])
            $ecwid_aff_tracking_insert    = '<script> xAffiliate("' . 'aff_id:' . $curr_aff_raw_id . ',aff_name:' . $curr_aff_username . ',cust_ip:' . $_SERVER['REMOTE_ADDR'] . '"); </script>';
         else
            $ecwid_aff_tracking_insert    = '<script> xAffiliate("' . $curr_aff_raw_id . '"); </script>';

         $content = preg_replace ($ecwid_search_string_regex, $ecwid_aff_tracking_insert . $ecwid_search_string, $content);
         }
      }

   if ($mwx_settings['memberwing_legacy_compatibility_mode'])
      {
      // Adjust legacy memberwing premium markers to MemberWing-X style premium conditions strings
      $content = preg_replace ('@\{\+\+\+\+\}@', '{{{platinum}}}',                     $content);
      $content = preg_replace ('@\{\+\+\+\}@',   '{{{gold|platinum}}}',                $content);
      $content = preg_replace ('@\{\+\+\}@',     '{{{silver|gold|platinum}}}',         $content);
      $content = preg_replace ('@\{\+\}@',       '{{{bronze|silver|gold|platinum}}}',  $content);
      }

   // This will help solve problem with excerpts removing tags and cutting off premium warning message in the middle.
   $display_none_prefix = '<div style="display:none;">';
   // <div style="display:none;">{{{...}}}</div>   ->   {{{...}}}
   $content = preg_replace ('@' . preg_quote($display_none_prefix) . '(\{\{\{.+?\}\}\})</div>@', "$1", $content);

   $premium_content = false;
   $user_allowed    = false;
   $reg_exp      = '@\{\{\{(.+?)\}\}\}@';

   $premium_marker_found = preg_match ($reg_exp, $content, $matches, PREG_OFFSET_CAPTURE);

   if (!$premium_marker_found)
      {
      // No premium marker present. See if one of article's category has category-wide premium marker defined.
      // If yes, we will force-inject premium marker inside $content.

      // Get array of categories for the current post/page:
      $categories = get_the_category ();

      $force_markers  = array();
      $teaser_lengths = array();
      foreach ($categories as $category)
         {
         if (isset($mwx_settings['categories_settings'][$category->term_id]['premium_marker']) && $mwx_settings['categories_settings'][$category->term_id]['premium_marker'])
            {
            $force_markers[]  = trim($mwx_settings['categories_settings'][$category->term_id]['premium_marker'], "\n\r\t |");
            $teaser_lengths[] = $mwx_settings['categories_settings'][$category->term_id]['teaser_length'];
            }
         }
      if (count($force_markers))
         {
         // Inject marker inside $content, using combined marker and minimal teaser length
         // ...
         $content = MWX__InjectPremiumMarkerInsideHTMLContent ($content, implode ('|', $force_markers), min($teaser_lengths));

         // Rescan $content
         $premium_marker_found = preg_match ($reg_exp, $content, $matches, PREG_OFFSET_CAPTURE);
         }
      }

   if ($premium_marker_found)
      {
      // Premium marker found => premium article.
      $premium_content     = TRUE;
      $marker_offset       = $matches[0][1];
      $conditions_string   = $matches[1][0]; // "4.95|gold" for input: {{{4.95|gold}}}

      // Returns: FALSE-cannot, or array ('immediate_access'=>FALSE, 'in_seconds'=>12345)
      // or: array('ip_protection_check' => 'failed')
      $user_can_access_article = MWX__UserCanAccessArticle ($id, $user_id, $conditions_string, $mwx_settings);
      $user_can_access_article_immediately = @$user_can_access_article['immediate_access'];

      if ($user_can_access_article_immediately || MWX__FCF_current_page_is_first_click_free ($mwx_settings))
         {
         // Logged on member already purchased the article                             OR
         // is a member of eligible membership product (including free membership)     OR
         // reached maturity                                                           OR
         // came from search engines while First Click Free feature is enabled
         $user_allowed = true;
         }
      }
   else
      {
      $user_allowed = true;   // Not a premium content.
      }

   if ($user_allowed)
      {
      if ($premium_content)
         $content = preg_replace ($reg_exp, "", $content); // Melt premium marker
      return $content;
      }

   //TODO: Somehow pass message about IP protection failure here to customize error message.

   // Current user is not authorized to view this article. Hide it behind premium content warning
   $warn_msg = MWX__AssemblePremiumContentWarningMessage ($id, $user_id, $conditions_string, @$user_can_access_article['in_seconds'], $user_can_access_article);

   // Before content - free teaser.
   $free_teaser = substr ($content, 0, $marker_offset);

   // Last part - build terminating tags that are left unterminated within teaser. This is necessary to keep HTML from breaking up.
   $trailer = MWX__BuildTrailingTags ($free_teaser);

   // Final assembly
   // Fix issues with excerpt screwing premium content warning message.
   if ((!$mwx_settings['show_premium_content_warning_for_home_page'] && is_home()) || (!$mwx_settings['show_premium_content_warning_for_category_pages'] && is_category()) || is_feed())
      $content = $free_teaser . " ..." . $trailer; // Port from MW 4.x. "Clean cut" method.
   else
      {
      // This part may need to go
      //                                [                                                               ]
      $content = $free_teaser . " ..." . $display_none_prefix . '{{{' . $conditions_string . '}}}</div>' . $warn_msg . $trailer;
      }

   return $content;
}

//---------------------------------------------------------------------------
// Assists in automatic integration code insertion for Paypal Buttons
function MWX__preg_replace_callback_Paypal ($match)
{
   global $g_MWX__paypal_ipn_url;
   $match = $match[0];
   $addition="";
   if (stripos ($match, 'name="notify_url"') === FALSE)
      $addition .= '<input type="hidden" name="notify_url" value="' . $g_MWX__paypal_ipn_url . '">' . "\n";
   if (stripos ($match, 'value="MWX_AFFILIATE_TRACKING_DATA"') === FALSE)
      $addition .= '<input type="hidden" name="custom" value="MWX_AFFILIATE_TRACKING_DATA">' . "\n";
   if ($addition)
      // Note: str_ireplace is better, but only avail in PHP 5
      $match = str_replace ('</form>', $addition . '</form>', $match);

   return  $match;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
// Assists in automatic integration code insertion for NMI Buttons
function MWX__preg_replace_callback_NMI ($match)
{
   global $g_MWX__paypal_ipn_url;
   $match = $match[0];
   $addition="";
   if (stripos ($match, 'value="MWX_AFFILIATE_TRACKING_DATA"') === FALSE)
      $addition .= '<input type="hidden" name="custom" value="MWX_AFFILIATE_TRACKING_DATA">' . "\n";
   if ($addition)
      // Note: str_ireplace is better, but only avail in PHP 5
      $match = str_replace ('</form>', $addition . '</form>', $match);

   return  $match;
}
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
// $premium_marker - stuff inside {{{...}}} brackets but without brackets
function MWX__InjectPremiumMarkerInsideHTMLContent ($content, $premium_marker, $teaser_length)
{
   $teaser_length = (int)$teaser_length;
   if ($teaser_length == 0)
      {
      return "{{{" . $premium_marker . "}}}" . $content; // Quick cover of this case.
      }

   $content_len = strlen ($content);

   // Find spot where to inject premium marker.
   for ($current_char_is_clean=TRUE, $abs_char=0, $clean_chars=0; $abs_char<$content_len && $clean_chars<$teaser_length; $abs_char++, $clean_chars++)
      {
      if ($current_char_is_clean)
         {
         if ($content[$abs_char] == '<')
            {
            $current_char_is_clean = FALSE;
            $clean_chars--;
            }
         }
      else
         {
         $clean_chars--;
         if ($content[$abs_char] == '>')
            {
            $current_char_is_clean = TRUE;
            }
         }
      }

   // Find gap in between words
   for (; $abs_char<$content_len; $abs_char++)
      {
      $content_char_ord = ord(strtoupper($content[$abs_char]));
      // A-Z or 0-9 ?
      if (($content_char_ord >= 65 && $content_char_ord <= 90) || ($content_char_ord >= 48 && $content_char_ord <= 57))
         continue;
      break;
      }

   if ($abs_char >= $content_len)
      {
      // Article is shorter or equal the length of requested teaser length.
      return $content;
      }
   else
      {
      // $abs_char now points to character in front of which we need to insert marker
      $content = substr ($content, 0, $abs_char) . "{{{" . $premium_marker . "}}}" . substr ($content, $abs_char);
      return $content;
      }
}
//---------------------------------------------------------------------------
//===========================================================================

//===========================================================================
//
// NOTE: '[mwx-list-premium-files]' tag is deprecated. Use '[mwx-digital-online-store]' multifunctional tag instead.

// Example: '[mwx-digital-online-store]'     (to show 't1' template)
//          or:
//          '[mwx-digital-online-store t5]'  (to show 't5' template)

function MWX__the_content_digital_online_store ($content = '')
{
   if (strpos($content, '[mwx-digital-online-store') === FALSE)
      return $content;  // Quick elimination of non matching pages.

   // Replace all occurences of dos tags: '[mwx-digital-online-store]', '[mwx-digital-online-store t5]' and similars with HTML code.
   $content = preg_replace_callback ('@\[mwx-digital-online-store\s*([^\s\]]*)\]@', 'MWX__DOS_InlineBuilder', $content);

   return ($content);
}

function MWX__DOS_InlineBuilder ($matches)
{
   $mwx_settings = MWX__get_settings ();

   $template = $matches[1]?$matches[1]:'t1';

   $css_js = "";
   if ($template != 't1')
      {
      if (isset($mwx_settings['dos_templates'][$template]['dos_style_stylesheet_file']))
         $css_js .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $mwx_settings['dos_templates'][$template]['dos_style_stylesheet_file'] . "\" />";

      if (isset($mwx_settings['dos_templates'][$template]['dos_style_javascript_file']))
         $css_js .= "<script type=\"text/javascript\" src=\"" . $mwx_settings['dos_templates'][$template]['dos_style_javascript_file'] . "\"></script>";
      }

   return $css_js . MWX__digital_online_store (array ('format'=>'html', 'use_template'=>$template));
}
//===========================================================================

//===========================================================================
// Execute custom PHP snippets inserted via custom fields in format:
// {{MWXPHP=name-of-custom-field-with-php-snippet-code-without-php-tags}}
// {{MWXPHP=my_php_snippet}}
//
function MWX__the_content_custom_PHP ($content = '')
{
     $content = preg_replace_callback ('|\{\{MWXPHP=(.*?)\}\}|s', 'MWX__MWXPHP_replacer', $content);
     return $content;
}

function MWX__MWXPHP_replacer ($matches)
{
   $php_code = get_post_custom_values($matches[1]);
   if ($php_code && is_array($php_code))
      {
      $php_code = $php_code[0];

      ob_start();
      eval ($php_code);
      $php_res = ob_get_contents();
      ob_end_clean ();
      }
   else
      $php_res = "";

   return $php_res;
}
//===========================================================================


//===========================================================================
function MWX__BuildTrailingTags ($teaser)
{
   // Melt beginning text
   $teaser = preg_replace ('@^[^<]+@', '', $teaser);

   // Melt ending text
   $teaser = preg_replace ('@[^>]+$@', '', $teaser);

   // Melt <script ... </script> stuff
   $teaser = preg_replace ('@<script.*?>.*?</script>@s', '', $teaser);

   // Kill everything in between tags
   $teaser = preg_replace ('@>[^<]+@', '>', $teaser);

   // Kill all self terminating tags
   $teaser = preg_replace ('@<[^>]*/>@', '', $teaser);

   // Melt inner attributes of tags.
   $teaser = preg_replace ('@<([a-zA-Z0-9]+)\s[^>]+>@', "<$1>", $teaser);

   // Get rid of unterminated <br> and <hr> tags.
   $teaser = preg_replace ('@<[bBhH][rR]>@', '', $teaser);

   // Repeat killing immediate tag pairs <h3></h3> until none exist.
   $count = 0;
   do
      {
      $teaser = preg_replace ('@<([a-zA-Z0-9]+)></\1>@', '', $teaser, -1, $count);
      }
   while ($count);

   // Fix possibly invalid markup: '<div><div><div></p></ul></div><div><div>' => '<div><div><div><div>'
   $teaser = preg_replace ('@<([a-zA-Z0-9]+)></.*?</\1>@', '', $teaser);

   // Final processing. Reverse tags order and add termination </ to each of them.
   $teaser = str_replace('<', '</', implode(array_reverse(explode ('[@]', str_replace ('><', '>[@]<', $teaser)))));

   return $teaser;
}
//===========================================================================

//===========================================================================
//
// $conditions_string - what's inside of {{{...}}} brackets.
// Returns: FALSE-cannot, or array ('immediate_access'=>FALSE, 'in_seconds'=>12345) or array('ip_protection_check' => 'failed')
// If $article_id == -1  - use current article
// If $user_id == -1     - use currently logged on user
//

function MWX__UserCanAccessArticle ($article_id, $user_id, $conditions_string, $mwx_settings = FALSE)
{
   if ($article_id == -1)
      {
      global $id;                      // ID of current article/post/page
      $article_id = $id;
      }

   if ($user_id == -1)
      {
      global $current_user;            // currently logged-on user
      $user_id = @$current_user->ID;    // All user's data: $current_user_data = get_userdata ($current_user->ID);
      }

   if (!$mwx_settings)
      {
      $mwx_settings = MWX__get_settings ();
      }

   // Get global settings array
   // This will assemble normalized array of product keywords and their delays in seconds: array ('keyword1' => 1234, 'keyword2' => 5678, ...)
   $global_prods_delays = MWX__GetProductsAccessDelays ($mwx_settings);

   //---------------------------------------
   // Build specified (required) products and delays arrays. Required == specified within the marker list of products.
   $required_prods_delays = array();
   $conditions = explode ('|', $conditions_string);
   foreach ($conditions as $condition)
      {
      $condition_and_delay = explode (':', $condition);
      $next_condition = trim($condition_and_delay[0]);
      $next_delay     = MWX__ConvertProdTimeToSeconds (@$condition_and_delay[1]); // 0 will yield 0, NULL or false will yield FALSE (value is not specified).
      $required_prods_delays[$next_condition] = $next_delay;
      }

   // Prepare array of p/d
//{{{PHP_DO_NOT_ENCODE}}}
   // Set all delays to zero for non-TSI version
   $is_tsi = MWX__License_TSI ($mwx_settings);
   if (!$is_tsi)
      {
      foreach ($required_prods_delays as $k=>$v)
         {
         $required_prods_delays[$k] = 0;
         }
      }
//{{{/PHP_DO_NOT_ENCODE}}}
   //---------------------------------------

   if ($user_id < 1 || (MWX__is_user_admin() && $mwx_settings['admin_acts_like_regular_visitor']))
      {
      // Here we have non-logged on user. The only way he can access article if {{{?}}} is specified.
      // Even if it was specified - we need to consider delays. Do that.
      if (isset($required_prods_delays['?']))
         {
         if ($required_prods_delays['?'] > 0)
            // >0 delay was specified on command line
            return MWX__ConvertAbsoluteDelayStructureToCurrentDelayStructure (array ('immediate_access'=>FALSE, 'in_seconds'=>$required_prods_delays['?']));

         if ($required_prods_delays['?'] === FALSE && @$global_prods_delays['?'] > 0)
            // Delay was NOT specified on command line but was specified in global settings.
            return MWX__ConvertAbsoluteDelayStructureToCurrentDelayStructure (array ('immediate_access'=>FALSE, 'in_seconds'=>$global_prods_delays['?']));

         // Both are FALSE, or required (command line) is set to '0'.
         return array ('immediate_access'=>TRUE,  'in_seconds'=>0);
         }
      else
         // Condition {{{?}}} was not specified. No access for non-logged on user.
         return FALSE;  // Non logged on visitor (or admin acting like visitor) cannot see any premium post/page at any time.
      }

   if (MWX__is_user_admin())
      return array ('immediate_access'=>TRUE,  'in_seconds'=>0);

    // IP protection check here
    if (@$mwx_settings['ip_protection_enabled'])
    {
    	$user_is_allowed = true;
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__UserCanAccessArticle(): NOTE: IP protection enabled. verifying user's IP ...");
//////////////////////!!!

			$user_ip_data = MWX__get_usermeta_array ($user_id, 'user_ip_data');
			if (is_array($user_ip_data))
			{
				$registered_ips = @$user_ip_data['registered_ips'];
				if (is_array($registered_ips) && count($registered_ips) >= $mwx_settings['ip_protection_max_allowed_addresses'])
				{
					// 'registered_ips' array is full. Make sure that either user's current IP or user's last logged-on-from IP ($_SESSION['login_ip']) are within 'registered_ips' array.
					// Last chance for him to have access is that if his current IP is within the first numbers of allowed IP's.
		    	$user_ip = MWX__get_visitor_REMOTE_ADDR();
		    	$user_is_allowed = false; // Now user must earn right to access premium content
		    	foreach ($registered_ips as $idx => $ip_info)
		    	{
						if (MWX__ip_addresses_matching ($user_ip, @$ip_info['REMOTE_ADDR'], $mwx_settings['ip_protection_cidr_mask']))
						{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__UserCanAccessArticle(): User is allowed because his current IP: '$user_ip' matches IP recorded at 'registered_ips' entry with index '$idx': '" . $ip_info['REMOTE_ADDR'] . "'");
//////////////////////!!!
							$user_is_allowed = true;
							break; // IP address match! IP protection check passed.
						}

						if (@$_SESSION['login_ip'] && MWX__ip_addresses_matching ($_SESSION['login_ip'], @$ip_info['REMOTE_ADDR'], $mwx_settings['ip_protection_cidr_mask']))
						{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__UserCanAccessArticle(): User is allowed because his first used login IP: '" . $_SESSION['login_ip'] . "' matches IP recorded at 'registered_ips' entry with index '$idx': '" . $ip_info['REMOTE_ADDR'] . "'");
//////////////////////!!!
							$user_is_allowed = true;
							break; // IP address match! IP protection check passed.
						}
		    	}

		    	if (!$user_is_allowed)
		    	{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__UserCanAccessArticle(): User is DISALLOWED because neither his first used login IP: '" . $_SESSION['login_ip'] . "' nor his current IP '" . $user_ip . "' matches any records within 'registered_ips' array.");
//////////////////////!!!
    				return array('ip_protection_check' => 'failed'); // Disallowed due to IP protection!
    			}
		    }
		    else
		    {
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__UserCanAccessArticle(): User is allowed because 'registered_ips' is not full yet. Currently contains " . count($registered_ips) . "/" . $mwx_settings['ip_protection_max_allowed_addresses'] . " entries.", serialize($registered_ips));
//////////////////////!!!

		    	// User was logged on from within allowed number of IP addresses.
		    	// So even if his current IP address is different - this is ok
		    	// Case: User uses original browser where he was already logged on from allowed location and is now traveling somewhere...
		    	;
		    }
			}
  	}
  	else
  	{
//////////////////////!!!
///MWX__log_event (__FILE__, __LINE__, "MWX__UserCanAccessArticle(): NOTE: IP protection is disabled.");
//////////////////////!!!
  	}

   // Samples of premium markers:
   // ===========================
   //    No product names can begin with '0', '*', '@'
   //    {{{*}}} or {{{0.00}}} or {{{0}}} -  just become a free member of blog to see this article
   //    {{{4.95}}}                 -  buy access to this article for 4.95
   //    {{{gold}}}                 -  must be a subscriber to "gold membership" to access this article
   //    {{{4.95|gold}}}            -  can either buy this article for 4.95 OR be a subscriber to "gold membership" to access it.
   //    {{{2.95|silver|gold}}}     -  can either buy this article for 2.95 OR be a subscriber to either "silver" or "gold" memberships to access it.
   //    {{{2.95|membership}}}      -  can either buy this article for 2.95 OR be a subscriber to any "membership" such as: "silver membership" or "my super membership plan" to access it. Case insensitive substring search within membership product name.
   //    {{{4.95|gold}}}            -  can either buy this article for 2.95 OR be a subscriber to "gold" to see this article
   //    {{{beginner}}}             -  must be a subscriber to "beginner student" to see this article.
   //    {{{$}}}                    -  could own any active product, subscription, article or page to see this article.
   //

///!!!MATURITY CHECK HERE
/*
   foreach ($conditions as $condition_short_string)
      {
      $sub_conditions = explode ('&', $conditions_string);
      if (MWX__substr_in_array ('maturity=', $sub_conditions))
         {
         ... it is either mnaturity since join (maturity=) or maturity belonging to product specified in array (gold&maturity=)
         }
      }
*/

   // $products_purchased is: array of purchased items:
   // array (array('product_id'=>'5', 'product_name'=>'', 'date'=>'2009-12-02', 'txn_id'=>array(...), 'subscr_id'=>'', 'active'=>'1'), array(...), ...)
   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

   // $allowed_access_info - structure containing matches between required products and something that user owns.
   // Array of matched keywords-to-products + UNIX times - time delays specified directly within marker. Different products might have different delays to access.
   // If no delay was specified on the command line - global settings will be used.
   // If delay was specified on the command line - it will override global settings
   // Marker like this: {{{$}}} will iterate through all products user owns and shortest delay will be picked.
   // Shortest delay will be picked at the end.
   $allowed_access_info = array();

   //---------------------------------------
   // Check for legacy MemberWing if needed. Convert MW 4.x user's level to MWX "product ownership"
   if ($mwx_settings['memberwing_legacy_compatibility_mode'])
      {
      foreach ($required_prods_delays as $required_product=>$delay)
         {
         if ($required_product == 'bronze' || $required_product == 'silver' || $required_product == 'gold' || $required_product == 'platinum')
            {
            if (current_user_can ('read_'.$required_product))
               {
               // User is at legacy level (bronze/silver/gold/...). Consider him owning related product.
               $allowed_access_info[$required_product] = $delay;
               continue;
               }
            }
         }
      }
   //---------------------------------------

   if (is_array($products_purchased))
      {
      foreach ($products_purchased as $product)
         {
         if (!MWX__is_product_active($product['product_status']))
            continue;   // Skip inactive products.

         // Product ID matches current page OR product name matches required subscription/conditions(maturity).
         if ((string)@$product['product_id'] !== '' && (int)$article_id == (int)$product['product_id'])
            // Direct purchase of article always allow immediate access. No need to check for time delays.
            return array ('immediate_access'=>TRUE, 'in_seconds'=>0);

         // Try to match name of one of purchased subscriptions/product to the name of one of required subscriptions/product.
         foreach ($required_prods_delays as $required_product=>$delay)
            {
            if (!$required_product)
               continue;   // Erratic value, likely like this: {{{|blah}}}. It means {{{}}} will "deny access to everyone regardless of products owned"

            if ($required_product == '?' || $required_product == '0' || $required_product == '*' || $required_product == '$')
               {
               // {{{?}}} - means any user, even non-logged on user. TRUE
               // {{{*}}} - means any logged on user. TRUE
               // {{{$}}} allows access to holder of any product. TRUE.
               $allowed_access_info[$required_product] = $delay;
               continue;
               }

            //  stristr ($haystack, $needle)
            if (!is_numeric($required_product) && stristr ($product['product_name'], $required_product))
               {
               // User owns eligible product
               $allowed_access_info[$required_product] = $delay;
               continue;
               }
            }
         }
      }

   // After all products were scanned/matched - check the state
   if (!count($allowed_access_info))
      {
      // Here we have logged on user that does not own any products eligible to give him access. The only way he can access article if {{{*}}} or {{{?}}} is specified.
      // Even if it was specified - we need to consider delays. Do that.
      $possible_delays = array();
      if (isset($required_prods_delays['?']))
         {
         if ($required_prods_delays['?'] > 0)
            {
            // >0 delay was specified on command line
            $possible_delays[] = $required_prods_delays['?'];
            }
         else if ($required_prods_delays['?'] === FALSE && @$global_prods_delays['?'] > 0)
            {
            // Delay was NOT specified on command line but was specified in global settings.
            $possible_delays[] = $global_prods_delays['?'];
            }
         else
            {
            // Both are FALSE, or required (command line) is set to '0'.
            return array ('immediate_access'=>TRUE,  'in_seconds'=>0);
            }
         }
      if (isset($required_prods_delays['*']))
         {
         if ($required_prods_delays['*'] > 0)
            {
            // >0 delay was specified on command line
            $possible_delays[] = $required_prods_delays['*'];
            }
         else if ($required_prods_delays['*'] === FALSE && @$global_prods_delays['*'] > 0)
            {
            // Delay was NOT specified on command line but was specified in global settings.
            $possible_delays[] = $global_prods_delays['*'];
            }
         else
            {
            // Both are FALSE, or required (command line) is set to '0'.
            return array ('immediate_access'=>TRUE,  'in_seconds'=>0);
            }
         }
      if (!count($possible_delays))
         {
         // Neither {{{?}}} nor {{{*}}} condition was specified. No access for logged on user who does not own anything
         return FALSE;  // Non logged on visitor (or admin acting like visitor) cannot see any premium post/page at any time.
         }

      return MWX__ConvertAbsoluteDelayStructureToCurrentDelayStructure (array ('immediate_access'=>FALSE, 'in_seconds'=>min ($possible_delays)));
      }

   //---------------------------------------
   // Iterate over matching products, calculate delays considering locally specified values and global delay settings, pick final minimal delay and deliver final judgement
   if (!count($allowed_access_info))
      return FALSE;  // User does not own anything to help him to access content

   // Possibly minimize delays.
   // User owns at least one eligible product. Now consider delays. Locally specified delay will override global setting for this keyword.
   // Please note that delay of 0 - means immediate access regarldess of global setting.
   //    While delay of FALSE - means local delay was not specified, hence global setting will be considered.
   // Adjust delay settings to minimals in list of matched products
   foreach ($allowed_access_info as $matched_product_keyword=>$product_delay)
      {
      // $allowed_access_info_element[0] could be: FALSE (?:20m), $ (owns any product) or Gold (or any other keyword).
      // Admin setting:
      //    'products_access_delays' => "?:30d\n*:14d\n$:10d\nBronzeTrader:7d\nSilverTrader:24h\nGoldTrader:20m",
      if ($product_delay===FALSE)
         {
         // Time delay was not specified on command line
         if (array_key_exists ($matched_product_keyword, $global_prods_delays))
            // Global setting *was* specified for that product. Use it.
            $allowed_access_info[$matched_product_keyword] = $global_prods_delays[$matched_product_keyword];
         else
            {
            // Neither command line not global value delay was specified for this product. Use 0 - no delay.
            // This also means immediate access
            // $allowed_access_info[$matched_product_keyword] = 0; // not necessary to actually do this step.
            return array ('immediate_access'=>TRUE, 'in_seconds'=>0);
            }
         }
      else
         {
         // Time delay *was* specified on command line. In all cases it overrides global delay.
         }
      }

   // Now find minimal value of all delays and returns it together with TRUE (allowed but with delay).
   $all_delays = array_values ($allowed_access_info);
   $delay_in_seconds = min($all_delays); // Danzig

   if ($delay_in_seconds>0)
      return MWX__ConvertAbsoluteDelayStructureToCurrentDelayStructure (array ('immediate_access'=>FALSE, 'in_seconds'=>$delay_in_seconds));
   else
      return array ('immediate_access'=>TRUE,  'in_seconds'=>0);
   //---------------------------------------
}
//===========================================================================

//===========================================================================
//
// Consider current article publish date and recalculate structure info. Return adjusted structure
// structure: array ('immediate_access'=>FALSE, 'in_seconds'=>12345)

function MWX__ConvertAbsoluteDelayStructureToCurrentDelayStructure ($access_delay_info_struct)
{
   if ($access_delay_info_struct['immediate_access'])
      return $access_delay_info_struct;

   if ($access_delay_info_struct['in_seconds'] == 0)
      {
      $access_delay_info_struct['immediate_access'] = TRUE;
      return $access_delay_info_struct;
      }

   global $post;
   $published_seconds_ago = time() - get_post_time('G', true, $post);

   $wait_required = $access_delay_info_struct['in_seconds'] - $published_seconds_ago;
   if ($wait_required < 0)
      {
      // Mandatory delay is expired for current article.
      $access_delay_info_struct['immediate_access']   = TRUE;
      $access_delay_info_struct['in_seconds']         = 0;
      }
   else
      {
      $access_delay_info_struct['immediate_access']   = FALSE;
      $access_delay_info_struct['in_seconds']         = $wait_required;
      }

   return $access_delay_info_struct;
}
//===========================================================================

//===========================================================================
//
// This function returns whether user is an admin taking into account multiuser
// sites and traditional single install sites
// returns boolean

function MWX__is_user_admin()
{
   if (function_exists('is_multisite') && is_multisite())
      {
      global $wpdb;
      $current_blog_id = $wpdb->blogid;
      return current_user_can_for_blog($current_blog_id, 'administrator');
      }
   else
      {
      return current_user_can ('edit_users');
      }
}
//===========================================================================

//===========================================================================
// $user_can_access_article_in_seconds:
//    FALSE:user is plain not allowed to access tihs article.
//    otherwise - specified delay in seconds after which user will be allowed to access this article.
function MWX__AssemblePremiumContentWarningMessage ($article_id, $user_id, $conditions_string, $user_can_access_article_in_seconds=FALSE, $extra_data = array())
{

   // Variables to initialize
   $current_page           = ltrim ((string)@$_SERVER['REQUEST_URI'], '/');
   $login_msg_required     = ($user_id > 0)?false:true;
      $separator1 = "";
   $subscribe_msg_premium_required = false;
   $subscribe_msg_free_required    = false;
      $separator2 = "";
   $buy_msg_required               = false;
   $item_price = 0;

   $mwx_settings = MWX__get_settings ();

   if (@$extra_data['ip_protection_check'] == 'failed')
   	return $mwx_settings['ip_protection_access_denied_message'];


   // Initialize '$subscribe_msg_premium_required' and '$buy_msg_required'.
   $conditions_arr = explode ('|', $conditions_string);
   foreach ($conditions_arr as $condition)
      {
      $condition = trim($condition);   // " $4.95" -> "4.95"

      if ((is_numeric($condition) && $condition == 0) || $condition == "*")
         {
         // This condition is either '0' or '*'. In this case "anyone can register" must be enabled in admin settings, and subscribe/register URL could be this: rtrim(get_bloginfo ('wpurl'), '/').'/wp-login.php?action=register
         $subscribe_msg_free_required = true;
         continue;
         }

      if (is_numeric($condition))
         {
         $buy_msg_required = true;   // Condition includes single price purchase.
         $item_price = $condition;
         }
      else
         {
         if (strncmp ($condition, 'maturity=', 9))
            $subscribe_msg_premium_required = true;  // if conditon does not start with 'maturity=' thing - then it is name of membership product.
         }
      }

   // Presence of Free subscription option overrides any premium options.
   if ($subscribe_msg_free_required)
      {
      // Free subscription option overrides premium ones.
      $subscribe_msg_premium_required  = false;
      $buy_msg_required                = false;
      }

   // Initialize separators
   if ($login_msg_required)
      {
      if ($subscribe_msg_premium_required || $subscribe_msg_free_required)
         {
         if ($buy_msg_required)
            {
            $separator1 = ", ";
            $separator2 = " or ";
            }
         else
            $separator1 = " or ";
         }
      else if ($buy_msg_required)
         $separator1 = " or ";
      }
   else if (($subscribe_msg_premium_required || $subscribe_msg_free_required) && $buy_msg_required)
      $separator2 = " or ";


   $premium_content_warning_message = MWX__base64_decode($mwx_settings['premium_content_warning']);

   // Substitute variables with their proper values.

   // Assemble time-sensitive access message.
   if ($user_can_access_article_in_seconds>0)
      {
      // User cannot access this article only due to specified delay for his level (product owned).
      $premium_content_warning_message = preg_replace ('|\{ACCESS_DELAYED_MSG\}|', $mwx_settings['access_delayed_msg'],                     $premium_content_warning_message);
      $premium_content_warning_message = preg_replace ('|\{TIME_LEFT\}|',          human_time_diff(0, $user_can_access_article_in_seconds), $premium_content_warning_message);
      }
   else
      $premium_content_warning_message = preg_replace ('|\{ACCESS_DELAYED_MSG\}|', "", $premium_content_warning_message);

   if ($subscribe_msg_free_required)
      $premium_content_warning_message = preg_replace ('|\{PROMO_MSG\}|', $mwx_settings['promo_msg_free'],    $premium_content_warning_message);
   else
      $premium_content_warning_message = preg_replace ('|\{PROMO_MSG\}|', $mwx_settings['promo_msg_premium'], $premium_content_warning_message);

   $premium_content_warning_message = preg_replace ('|\{LOGIN_MSG\}|',     $login_msg_required?($mwx_settings['login_msg'].$separator1):"", $premium_content_warning_message);

   if ($subscribe_msg_premium_required)
      $premium_content_warning_message = preg_replace ('|\{SUBSCRIBE_MSG\}|', $mwx_settings['subscribe_msg_premium'].$separator2, $premium_content_warning_message);
   else if ($subscribe_msg_free_required)
      $premium_content_warning_message = preg_replace ('|\{SUBSCRIBE_MSG\}|', $mwx_settings['subscribe_msg_free'].$separator2,    $premium_content_warning_message);
   else
      $premium_content_warning_message = preg_replace ('|\{SUBSCRIBE_MSG\}|', "",                                                $premium_content_warning_message);

   $premium_content_warning_message = preg_replace ('|\{BUY_MSG\}|',       $buy_msg_required?$mwx_settings['buy_msg']:"", $premium_content_warning_message);

   // Substitute smaller variables.
   $premium_content_warning_message = preg_replace ('|\{CURRENT_PAGE\}|',  $current_page, $premium_content_warning_message);
   if ($subscribe_msg_premium_required)
      $premium_content_warning_message = preg_replace ('|\{SUBSCRIBE_URL_PREMIUM\}|', $mwx_settings['subscribe_url_premium'], $premium_content_warning_message);
   else if ($subscribe_msg_free_required)
      $premium_content_warning_message = preg_replace ('|\{SUBSCRIBE_URL_FREE\}|', $mwx_settings['subscribe_url_free'],    $premium_content_warning_message);
   $premium_content_warning_message = preg_replace ('|\{PRICE\}|',         $item_price, $premium_content_warning_message);

   if ($buy_msg_required)
      {
      // Returns: array ('buy_now_button_code'=>'...', 'add_to_cart_button_code'=>'...', 'view_cart_button_code'=>'...')
      if ($mwx_settings['authnet_postback_integration_enabled'] && $mwx_settings['default_primary_payment_processor']=='authorize.net')
         $paypal_buttons = MWX__CreateAuthorizeNetButtons ($article_id, get_the_title ($article_id), $item_price, $mwx_settings);
      else
         $paypal_buttons = MWX__CreatePaypalButtons ($article_id, get_the_title ($article_id), $item_price, $mwx_settings);

      $premium_content_warning_message = preg_replace ('|\{BUYCODE\}|', $paypal_buttons['buy_now_button_code'], $premium_content_warning_message);
      }

   if (@$mwx_settings['memberwing-x-license-info']['is_sponsored'] || @$mwx_settings['show-powered-by'])
      $premium_content_warning_message .= (string)@$mwx_settings['memberwing-x-license-info']['brd'];

   return ($premium_content_warning_message);
}
//===========================================================================

//===========================================================================
function MWX__CreateAuthorizeNetButtons ($article_id, $article_name, $price, $mwx_settings=FALSE)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

   // get base values
   $checkout_url_base = get_bloginfo ('wpurl') . '/'.get_option('authnet_checkoutpage');
   $subscription = 'single';

   // create checkout claim
   $securityseed = get_option('authnet_securityseed');
   $len = strlen($securityseed);
   $checkout_claim = md5(substr($securityseed, 0, round($len/2)) . $price.$article_name . substr($securityseed, round($len/2), $len));

   // create checkout URL
   $checkout_url = $checkout_url_base;
   $checkout_url .= '?subscription='.$subscription;
   $checkout_url .= '&amount='.$price;
   $checkout_url .= '&article_id='.$article_id;
   $checkout_url .= '&postname='.$article_name;
   $checkout_url .= '&claim='.$checkout_claim;


   $authnet_buttons = array();

   // Prepare custom data: flattened array with 'customer_ip', 'referred_by' info etc...
   $custom_data = MWX__PackCustomData ($mwx_settings['secret_password'], MWX__GetCurrentAffiliateRawID(), $price);

   $authnet_buttons['buy_now_button_code'] = '<a href="' . $checkout_url . '"><img src="' . $mwx_settings['buy_now_button_image'] . '"></a>';


   ///!!! Note: Add to Cart/View cart options aren't available with Authorize.net
   $authnet_buttons['add_to_cart_button_code'] = '';
   $authnet_buttons['view_cart_button_code'] ='';

   return $authnet_buttons;
}
//===========================================================================

//===========================================================================
//
// Build HTML code for paypal buttons. Returns: array ('buy_now_button_code'=>'...', 'add_to_cart_button_code'=>'...', 'view_cart_button_code'=>'...')
// If '$article_id' != 0 => $article_name = "Item: $article_name (id:$article_id)"
function MWX__CreatePaypalButtons ($article_id, $article_name, $price, $mwx_settings=FALSE)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

   // Check if current affiliate is due to be instantly paid for this sale.
   if (MWX__CurrentAffiliateInstantlyPayable ($price))
      {
      // Adaptive Payments - Chained payment will be made to merchant and affiliate.
      // Condition of Adaptive payment call:
      // -  referred by affiliate
      // -  affiliate reached payout conditions
      $payment_script_url = get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/mwx-paypal-x.php", __FILE__);
      $payment_script_url .= '?' . MWX__GenerateURLSecurityParam ();
      $payment_script_url .= '&' . MWX__URL_DebugStr ($mwx_settings);
      }
   else if ($mwx_settings['paypal_sandbox_enabled'])
      $payment_script_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
   else
      $payment_script_url = "https://www.paypal.com/cgi-bin/webscr";

   if ($mwx_settings['paypal_sandbox_enabled'])
      $business_email = $mwx_settings['paypal_sandbox_email'];
   else
      $business_email = $mwx_settings['paypal_email'];

   if ($article_id)
      $item_name = "Item: $article_name (id:$article_id)";
   else
      $item_name = $article_name;
   $paypal_ipn_url = $mwx_settings['paypal_ipn_url'];

   $paypal_ipn_url .= '?' . MWX__URL_DebugStr ($mwx_settings);

   $paypal_buttons = array();

   // Prepare custom data: flattened array with 'customer_ip', 'referred_by' info etc...
   $custom_data = MWX__PackCustomData ($mwx_settings['secret_password'], MWX__GetCurrentAffiliateRawID(), $price);

   $paypal_buttons['buy_now_button_code'] =<<<TTT
<form action="$payment_script_url" method="post" style="display:inline;">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="$business_email">
<input type="hidden" name="item_name" value="$item_name">
<input type="hidden" name="amount" value="$price">
<input type="hidden" name="currency_code" value="{$mwx_settings['paypal_currency_code']}">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="return" value="{$mwx_settings['payment_success_page']}">
<input type="hidden" name="cancel_return" value="{$mwx_settings['payment_cancel_page']}">
<input type="hidden" name="shipping" value="0.00">
<input type="hidden" name="custom" value="$custom_data">
<input type="hidden" name="notify_url" value="$paypal_ipn_url">
<input type="image" src="{$mwx_settings['buy_now_button_image']}" border="0" name="submit" alt="Buy '$item_name'. Instant access!" title="Powered by Paypal" style="margin:0;vertical-align:middle;border:none !important;width:inherit;">
</form>
TTT;


///!!! Note: Add to Cart/View cart options cannot be supported for Adaptive/Instant affiliate payments
   $paypal_buttons['add_to_cart_button_code'] =<<<TTT
<form target="paypal" action="$payment_script_url" method="post" style="display:inline;">
<input type="hidden" name="cmd" value="_cart">
<input type="hidden" name="business" value="$business_email">
<input type="hidden" name="item_name" value="$item_name">
<input type="hidden" name="amount" value="$price">
<input type="hidden" name="currency_code" value="{$mwx_settings['paypal_currency_code']}">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="return" value="{$mwx_settings['payment_success_page']}">
<input type="hidden" name="cancel_return" value="{$mwx_settings['payment_cancel_page']}">
<input type="hidden" name="shipping" value="0.00">
<input type="hidden" name="add" value="1">
<input type="hidden" name="custom" value="$custom_data">
<input type="hidden" name="notify_url" value="$paypal_ipn_url">
<input type="image" src="{$mwx_settings['add_to_cart_button_image']}" border="0" name="submit" alt="Buy '$item_name'. Instant access!" title="Powered by Paypal" style="margin:0;vertical-align:middle;border:none !important;width:inherit;">
</form>
TTT;

   $paypal_buttons['view_cart_button_code'] =<<<TTT
<form target="paypal" action="$payment_script_url" method="post" style="display:inline;">
<input type="hidden" name="cmd" value="_cart">
<input type="hidden" name="business" value="$business_email">
<input type="hidden" name="display" value="1">
<input type="hidden" name="custom" value="$custom_data">
<input type="hidden" name="notify_url" value="$paypal_ipn_url">
<input type="image" src="{$mwx_settings['view_cart_button_image']}" border="0" name="submit" alt="View shopping cart" title="Powered by Paypal" style="margin:0;vertical-align:middle;border:none !important;width:inherit;">
</form>
TTT;

   return $paypal_buttons;
}
//===========================================================================

//===========================================================================
//
// Function sets affiliate referrer cookie on the visitor's browser and returns ID of the current affiliate who refered this visitor.
// Cookie is set only if:
// -  Cookie is not already set
// -  Cookie was set before and has the same referrer (resets time)
//    $_GET['aff'] - is affiliate referrer
//
// Function returns affiliate id of the current affiliate referrer for the given visitor
//
// Your affiliates use their id on any page like this:
//    www.YOUR-SITE.com/some/page?mwxaid=12345
// or
//    www.YOUR-SITE.com/some/page?mwxaid=YOUR@EMAIL.COM

function MWX__SetCookie ()
{
   if (preg_match ('#(slurp|bot|sp[iy]der|scrub(by|the)|crawl(er|ing|@)|yandex)#i', @$_SERVER['HTTP_USER_AGENT']))
      return;  // For search engine spiders we don't set cookies.


   $mwx_settings = MWX__get_settings ();
   $aff_url_prefix = $mwx_settings['aff_affiliate_id_url_prefix'];

   // Read possible affiliate referrer id from query string. 'aff' is hardcoded for webmasters who
   //    changed affiliate URL prefix later on but don't want every affiliate to change all links.
   $immediate_referrer_id = isset($_GET[$aff_url_prefix])?$_GET[$aff_url_prefix]:(isset($_GET['aff'])?$_GET['aff']:"self");
   $immediate_referrer_id = urldecode ($immediate_referrer_id);

   // Pull second and third tier from URL
   $immediate_referrer_id_tier2 = @$_GET['aff2']?$_GET['aff2']:"";
   $immediate_referrer_id_tier3 = @$_GET['aff3']?$_GET['aff3']:"";
   $immediate_referrer_id_tier4 = @$_GET['aff4']?$_GET['aff4']:"";
   $immediate_referrer_id_tier5 = @$_GET['aff5']?$_GET['aff5']:"";

   // Read current cookie
   $mwx_cookie = isset($_COOKIE['memberwing-x'])?MWX__DecodeCookie($_COOKIE['memberwing-x']):array();

   // Check cookie expiration
   $cookie_expired = FALSE;
   if (isset($mwx_cookie['refinfo']['datetime']))
      {
      $cookie_age_days = floor((strtotime("now") - strtotime($mwx_cookie['refinfo']['datetime'])) / (60*60*24));
      if ($mwx_settings['aff_cookie_lifetime_days'] && $cookie_age_days > $mwx_settings['aff_cookie_lifetime_days'])
         $cookie_expired = TRUE;
      }

   // Set cookie only if true:
   // -  Cookie is not already set OR expired
   // OR cookie is already set AND:
   // First affiliate wins:
   // -  has the same referrer as immediate_referrer (this will reset time counter for the cookie)
   // Last affiliate wins:
   // -  cookie was not set by 'self'(webmaster)

   $reset_cookie = FALSE;

   if ($cookie_expired || !isset($mwx_cookie['refinfo']['mwxaid']))
      $reset_cookie = TRUE;   // Cookie expired or does not exist.
   else if ($immediate_referrer_id == 'self' && $mwx_cookie['refinfo']['mwxaid'] != 'self')
      $reset_cookie = FALSE;  // Direct visit but valid cookie already contains affiliate id.
   else
      {
      if ($mwx_settings['aff_first_affiliate_wins'])
         {
         if ($mwx_cookie['refinfo']['mwxaid'] == $immediate_referrer_id)
            $reset_cookie = TRUE;
         }
      else
         {
         // Last affiliate wins - means cookie of previous affiliate will be overwritten by new affiliate.
         // Note: allow overwriting of 'self' cookies by affiliate cookie from debugging machine or from sandbox mode.
         if ($mwx_cookie['refinfo']['mwxaid'] != 'self' || MWX__DebuggingComputer ($mwx_settings) || $mwx_settings['paypal_sandbox_enabled'])
            $reset_cookie = TRUE;
         }
      }

   if ($reset_cookie)
      {
      $mwx_cookie['refinfo']['mwxaid']    = $immediate_referrer_id;

      // Encode second and third tier in raw affiliate id.
      if ($immediate_referrer_id_tier2)
         $mwx_cookie['refinfo']['mwxaid'] .= ",$immediate_referrer_id_tier2";
      if ($immediate_referrer_id_tier3)
         $mwx_cookie['refinfo']['mwxaid'] .= ",$immediate_referrer_id_tier3";
      if ($immediate_referrer_id_tier4)
         $mwx_cookie['refinfo']['mwxaid'] .= ",$immediate_referrer_id_tier4";
      if ($immediate_referrer_id_tier5)
         $mwx_cookie['refinfo']['mwxaid'] .= ",$immediate_referrer_id_tier5";

      $mwx_cookie['refinfo']['datetime']  = date ('Y-m-d H:i:s T', strtotime ("now"));
      $mwx_cookie['refinfo']['referrer']  = substr((isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:""), 0, 256);

      @setcookie("memberwing-x", MWX__EncodeCookie ($mwx_cookie), strtotime("+5 years"), defined('SITECOOKIEPATH')?SITECOOKIEPATH:"/");
      }

   return $mwx_cookie['refinfo']['mwxaid'];

}
//===========================================================================

//===========================================================================
function MWX__EncodeCookie ($cookie)
{
   return base64_encode(serialize($cookie));
}
//===========================================================================

//===========================================================================
function MWX__DecodeCookie ($cookie)
{
   $cookie = unserialize(base64_decode($cookie));
   if (!is_array($cookie))
      $cookie = array();

   return $cookie;
}
//===========================================================================

//===========================================================================
// Hide comments of any article from all non-logged on users - depending on admin settings.

function MWX__flt_comments_number($x)
{
   if (!MWX__do_hide_comments())
      return $x;  // Settings saying: Do not hide comments from anyone.

   global $user_ID;
   wp_get_current_user();

   if (!$user_ID)
      {
      return 0;
      }

   return $x;
}
function MWX__flt_comments_array($content="")
{
   if (!MWX__do_hide_comments())
      return $content;  // Settings saying: Do not hide comments from anyone.

   global $user_ID;
   wp_get_current_user();

   if (!$user_ID)
      {
      //no user logged in - return empty array of comments.
      $content = array();
      }
   return ($content);
}

function MWX__flt_comments($content="")
{
   if (!MWX__do_hide_comments())
      return $content;  // Settings saying: Do not hide comments from anyone.

   global $user_ID;
   wp_get_current_user();

   if (!$user_ID)
      {
      //no user logged in - melt comments
      // Melt everything outside of tags, leaving all tags in tact. Tags == <...>
      $content = preg_replace ('#(?<=^|\>)[^\<]*#', '', $content);
      // Melt all inner attributes of tags
      $content = preg_replace ('/<([a-zA-Z0-9]+)\s[^>]*>/', "<$1>", $content);
      }
   return ($content);
}

function MWX__do_hide_comments()
{
   $mwx_settings = MWX__get_settings ();
   if ($mwx_settings['hide_comments_from_non_logged_on_users'])
      return TRUE;
   return FALSE;
}
//===========================================================================

//===========================================================================
// Create registration shortcode for advanced sign up strategies
// shortcode should look like this [mwx_auto_register_aweber prodkeyword=productkeyword requireterms=true|false]
// http://www.wordpressmembershipuniversity.com/wp-content/plugins/memberwing-x/affiliate-signup.php
// ?email=dwmaillist%40gmail.com
// &from=dwmaillist%40gmail.com
// &meta_adtracking=test_form
// &meta_message=1
// &name=
// &unit=wmu-affiliate
// &add_url=http://forms.aweber.com/form/74/1009872674.htm
// &add_notes=96.18.99.77
// &custom%20First=Daniel
// &custom%20Last=Watrous
// &custom%20Terms%20Agree=yes
// &custom%20Username=dwatrous
function MWX__auto_registration_aweber_shortcode ($atts, $content=null)
{
   $register_message = "";
   // extract attributes from the $atts passed in
   extract(shortcode_atts(array(
                     'prodkeyword' => '',
                     'requireterms' => 'false',
                     ), $atts));

   // extract _GET values
   $email = (isset($_GET['email'])) ? $_GET['email']:'';
   $firstname = (isset($_GET['custom_First'])) ? $_GET['custom_First']:'';
   $lastname = (isset($_GET['custom_Last'])) ? $_GET['custom_Last']:'';
   $username = (isset($_GET['custom_Username'])) ? $_GET['custom_Username']:'';
   $termsagree = (isset($_GET['custom_Terms_Agree'])) ? $_GET['custom_Terms_Agree']:'';
   $ipaddress = (isset($_GET['add_notes'])) ? $_GET['add_notes']:'';
   $listname = (isset($_GET['unit'])) ? $_GET['unit']:'';
   $registration_date = date ('Y-m-d H:i:s', strtotime ("now"));
   if ($email == ''
//      || $firstname == ''
//      || $lastname == ''
//      || $username == ''
      || $ipaddress == ''
      || $listname == '') {
      return '<font color="red"><strong>Invalid details. Unable to create account.</strong></font>';
   }

   // setup and sanitize inputs
   global $_inputs;

   MWX__ResetInputs ($_inputs);

   $_inputs['first_name']       = $firstname;                   // Buyer
   $_inputs['last_name']        = $lastname;                  // Buyer
   $_inputs['payer_email']      = $email;         // Buyer  (test buyer - your other email)
   $_inputs['receiver_email']   = get_bloginfo('admin_email');     // Seller (you, webmaster, website owner)
   $_inputs['desired_username'] = $username;               // Buyer's username (optional)
   $_inputs['desired_password'] = wp_generate_password();                // Buyer's password (optional)
   $_inputs['U_txn_date']       = $registration_date; // Normalize it for database usage.
   $_inputs['txn_id']           = md5($email.$listname.$ipaddress);
   $_inputs['txn_type']         = 'subscr_signup';
   $_inputs['item_name']        = $prodkeyword;
   $_inputs['mc_amount3_gross'] = "0.00";                  // Amount paid
   $_inputs['customer_ip']      = $ipaddress;            // Buyer's IP address

   // create user account and assign membership privileges
   if ($requireterms == 'false' || ($requireterms == 'true' && $termsagree == 'yes')) {
      MWX__Product_Purchased ();  // Single item or subscription purchased. Enter product in user's metadata.
      return "<font color=green><strong>Registration Complete</strong></font>";
   } else return "<font color=red><strong>You must accept affiliate terms.</strong></font>";
}
//===========================================================================


?>