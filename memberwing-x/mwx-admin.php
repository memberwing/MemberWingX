<?php

global $g_MWX__default_welcome_email;
$g_MWX__default_welcome_email =
   'Dear {FIRST_NAME} {LAST_NAME}, your purchase of "{ITEM_NAME}" is confirmed.' .
   '<br />Your username / password are: {USERNAME} / {PASSWORD}' .
   '<br />Click here to Login: {BLOG_LOGIN_URL}' .
   '<br />After you login you will be able to see content that you\'ve purchased access to.' .
   '<br /><br />Please contact us for any questions! Sincerely,<br/>{BLOG_ROOT_URL}';

// '{CURRENT_PAGE}' is current page URL without domain name and front slash.
global $g_MWX__default_premium_content_warning;
$g_MWX__default_premium_content_warning =
   '<div style="background-color:#FFC;padding:3px;border:2px solid #FFCCCC;margin:0 0 5px;font-size:small;">' .
   '{PROMO_MSG}<div>{LOGIN_MSG} {SUBSCRIBE_MSG} {BUY_MSG}</div></div>';

global $g_MWX__plugin_directory_url;
$g_MWX__plugin_directory_url = get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1", __FILE__);

global $g_MWX__paypal_ipn_url;
$g_MWX__paypal_ipn_url = $g_MWX__plugin_directory_url . '/mwx-notify-paypal.php';

global $g_MWX__config_defaults;
$g_MWX__config_defaults = array (
// ------- General Settings
   'memberwing-x-license_code'            => '',
   'your-memberwing-affiliate-link'       => '',
   'show-powered-by'                      => '1',
   'keep_access_for_ended_subscriptions'  => '1',  // Keep access active if subscription ended normally. If "0"-access will auto-stop
   'payment_success_page'                 => get_bloginfo ('wpurl'),
   'payment_cancel_page'                  => get_bloginfo ('wpurl'),
   'buy_now_button_image'                 => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/buy_now.gif",     __FILE__),
   'add_to_cart_button_image'             => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/add_to_cart.gif", __FILE__),
   'view_cart_button_image'               => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/view_cart.gif",   __FILE__),
   'premium_content_warning'              => MWX__base64_encode ($g_MWX__default_premium_content_warning),
   'login_msg'                            => '<a href="' . rtrim(get_bloginfo ('wpurl'), '/') . '/wp-login.php?redirect_to=/{CURRENT_PAGE}">Login</a>',
   'subscribe_url_premium'                => rtrim(get_bloginfo ('wpurl'), '/') . '/subscribe/',
   'subscribe_msg_premium'                => '<a href="{SUBSCRIBE_URL_PREMIUM}">Subscribe</a>',
   'subscribe_url_free'                   => rtrim(get_bloginfo ('wpurl'), '/') . '/wp-login.php?action=register',
   'subscribe_msg_free'                   => '<a href="{SUBSCRIBE_URL_FREE}">Register</a>',
   'promo_msg_premium'                    => 'The rest of this article is available to premium members only.',
   'promo_msg_free'                       => 'The rest of this article is available to members. Membership is Free!',
   'buy_msg'                              => 'Buy this article: <span style="font-weight:bold;color:#080;">${PRICE}  </span> {BUYCODE}',
   'welcome_email_subject'                => 'Thank you for your purchase',
   'welcome_email_body'                   => MWX__base64_encode ($g_MWX__default_welcome_email),
   'delete_emptyhanded_user'              => "0",
   'hide_comments_from_non_logged_on_users' => '1',
   'memberwing_legacy_compatibility_mode' => "1",  // Makes MemberWing-X compatible with old-style premium markers: {+++}
   'admin_acts_like_regular_visitor'      => '1',  // Enabled: admin will not see premium content if it is protected. Disabled: Admin sees all. Useful for testing.
   'mwx_api_endpoint'                     => $g_MWX__plugin_directory_url . '/mwx-api.php',  // URL of API endpoint
   'mwx_api_key'                          => substr(md5(microtime()), 0, 16), // Also used to protect custom data and prevent spoofing.

// ------- Paypal Settings
   'paypal_email'                         => get_option('admin_email')?get_option('admin_email'):get_settings('admin_email'),
   'paypal_currency_code'                 => 'USD',
   'paypal_ipn_url'                       => $g_MWX__paypal_ipn_url,
   'paypal_integration_code_html'         => '<input type="hidden" name="notify_url" value="' . $g_MWX__paypal_ipn_url . '">' . "\n" .
                                             '<input type="hidden" name="custom" value="MWX_AFFILIATE_TRACKING_DATA">',
   'paypal_integration_code_auto_insert'  => '1',
   'paypal_sandbox_enabled'               => '',
   'paypal_sandbox_email'                 => '',
   'sandbox_machine_ip_address'           => '123.45.6.78',

// ------- Autoresponders Settings
   'aweber_integration_enabled'           => "0",
   'aweber_list_email'                    => "",
   'mailchimp_integration_enabled'        => "0",
   'mailchimp_api_key'                    => "",
   'mailchimp_mail_list_id_number'        => "",
   'mailchimp_interest_groups'            => "",

// ------- Email Settings
   'smtp_enabled'                         => "0",
   'smtp_host'                            => "",
   'smtp_username'                        => "",
   'smtp_password'                        => "",
   'smtp_port'                            => "25",
   'smtp_use_authentication'              => "1",

// ------- Affiliate Settings
   'mwx_affiliate_network_enabled'        => '1',     // 0-no affiliate relationships are enabled. Affiliate sales still tracked and recorded.
   'aff_first_affiliate_wins'             => '0',     // Enabled: First affiliate who direct user to your site will be eventually credited for sale. Disabled: Only last affiliate who refers buying customer will be credited for sale.
   'aff_cookie_lifetime_days'             => '30',    // Number of days before affiliate cookie will be deleted and no longer considered for referral commissions.
   'aff_min_payout_threshold'             => '0',     // In $. 0-instant payment, other-balance must reach this level for payout to be triggered.
   'aff_promotion_to_zero_min_payout'     => '1',     // '0'-after payout is made - affiliate will need to accumulate min payout again. But if '1'-once affiliate reaches his payout threshold once - his personal payout threshold will be auto-set to zero (achieved min payout immunity). He won't need to accumulate threshold ever again to be paid. Payments will become instant for him.
   'aff_manual_aff_sale_approval'         => '0',     // 1-each sale made by each affiliate must be manually approved by webmaster. 0-automatically approved. Manual or Auto - Payout still need to reach min threshold to be paid out.
   'aff_manual_payouts'                   => '1',     // 'aff_manual_payouts' - 1-payouts done to affiliates manually by webmaster even if affiliate reached min threshold. 0-automatically when all other conditions are met.
   'aff_sale_auto_approve_in_days'        => '0',     // 0-each sale immediately approved. other-auto approved in that many days after sale. Once approved - total still need to reach min threshold to be paid out. If manual approval is set this has no power - approval always be manual.
   'aff_payout_percents'                  => '25',    // Percents off sale to pay for each affiliate.
   'aff_payout_percents2'                 => '5',     // Percents off sale to pay for each Tier2 affiliate.
   'aff_payout_percents3'                 => '0',     // Percents off sale to pay for each Tier3 affiliate.
   'aff_tiers_num'                        => '2',
   'aff_auto_approve_affiliates'          => '1',     // 1-each affiliate is auto-approved. 0-webmaster needs to manually approve every affiliate. Note: if auto approve is ON and sale is referred - &mwxaid=EMAIL-type of affiliate will automatically be added as a blog member during sale processing.
   'aff_affiliate_id_url_prefix'          => 'aff',   // goes to: www.your-site.com/any-page/?aff=john@smith.com OR www.your-site.com/any-page/?aff=123

// ------- Integration with Other Systems
   // ---- iDevAffiliate
   'idevaffiliate_integration_enabled'    => "0",
   'idevaffiliate_install_dirname'        => get_bloginfo ('wpurl') . '/idevaffiliate',

   // ---- InfusionSoft
   'infusionsoft_postback_integration_enabled' => "0",
   'infusionsoft_post_url'                => $g_MWX__plugin_directory_url . '/extensions/InfusionSoft/post.php?item_name=My+Gold+Membership',

// ------- Non-UI-ed settings.
   'secret_password'                      => substr(md5(microtime()), -16), // Also used to protect custom data and prevent spoofing.
   'memberwing-x-license-valid'           => "0",
   'brd'                                  => '<div align="center" style="font-size:9px;line-height:9px;padding:1px;margin:1px 0;border:1px solid #bbb;">Powered by <a href="{{{MEMBERWING_URL}}}" style="color:inherit;text-decoration:none;">wordpress membership plugin</a> <a href="{{{MEMBERWING_URL}}}" style="color:inherit;text-decoration:none;">MemberWing-X</a></div>',
   );

//===========================================================================
function MWX__base64_encode ($str) { return $str; }
function MWX__base64_decode ($str) { return $str; }
//===========================================================================

//===========================================================================
// Request forgery prevention
function MWX__GenerateURLSecurityParam () { return '_wpnonce=' . wp_create_nonce('mwx-keyonce'); }
function MWX__ValidateURLSecurityParam ()
{
// Security check
if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'mwx-keyonce'))
   {
   exit ('<html><body><h2 align="center" style="color:red;margin:50px;">Invalid or forged request. Aborted.</h2><h1 align="center" style="margin:50px;color:red;background-color:yellow">Your IP address(' . $_SERVER['REMOTE_ADDR'] . ') is logged for investigation.</h1></body></html>');
   }
}
//===========================================================================

//===========================================================================
function MWX__get_settings ()
{
   $mwx_settings = get_option ('MemberWing-X');
   if (isset($mwx_settings['premium_content_warning']))
      $mwx_settings['premium_content_warning'] = MWX__base64_decode ($mwx_settings['premium_content_warning']);

   if (isset($mwx_settings['welcome_email_body']))
      $mwx_settings['welcome_email_body']      = MWX__base64_decode ($mwx_settings['welcome_email_body']);

   //------------------------------------------------------------------------
   // Load forced-default settings
   global   $g_MWX__config_defaults;
   $mwx_settings['paypal_ipn_url']          = $g_MWX__config_defaults['paypal_ipn_url'];
   $mwx_settings['paypal_integration_code_html']    = $g_MWX__config_defaults['paypal_integration_code_html'];
   //------------------------------------------------------------------------

   return ($mwx_settings);
}
//===========================================================================

//===========================================================================
function MWX__update_settings ($mwx_use_these_settings="")
{
   if ($mwx_use_these_settings)
      {
      update_option ('MemberWing-X', $mwx_use_these_settings);
      return;
      }

   global   $g_MWX__config_defaults;

   // Load current settings and overwrite them with whatever values are present on submitted form
   $mwx_settings = MWX__get_settings();

   foreach ($g_MWX__config_defaults as $k=>$v)
      {
      if (isset($_POST[$k]))
         $mwx_settings[$k] = stripslashes($_POST[$k]);
      // If not in POST - existing will be used.
      }

   //---------------------------------------
   // Checkboxes needs to be processed manually and separately. If it is not set - then it is unchecked.
   // NOTE: solved by preceding each checkbox on form with this hidden value:
   //    <input type="hidden" name="checkbox-name" value="0" />
   // if (!isset($_POST['show-powered-by']))
   //    $mwx_settings['show-powered-by'] = "";
   //---------------------------------------

   //---------------------------------------
   // Validation
   $mwx_settings['premium_content_warning'] = MWX__base64_encode ($mwx_settings['premium_content_warning']);
   $mwx_settings['welcome_email_body']      = MWX__base64_encode ($mwx_settings['welcome_email_body']);
   $mwx_settings['aff_payout_percents']     = trim ($mwx_settings['aff_payout_percents'], ' %');
   $mwx_settings['aff_payout_percents2']    = trim ($mwx_settings['aff_payout_percents2'], ' %');
   $mwx_settings['aff_payout_percents3']    = trim ($mwx_settings['aff_payout_percents3'], ' %');
   $mwx_settings['aff_min_payout_threshold']     = trim ($mwx_settings['aff_min_payout_threshold'], ' $');
   $mwx_settings['paypal_ipn_url']          = $g_MWX__config_defaults['paypal_ipn_url'];
   $mwx_settings['paypal_integration_code_html']    = $g_MWX__config_defaults['paypal_integration_code_html'];

   if ($mwx_settings['aff_payout_percents'] < 5)
      $mwx_settings['aff_payout_percents'] = "5";
   else if ($mwx_settings['aff_payout_percents'] > 90)
      $mwx_settings['aff_payout_percents'] = "90";

   if ($mwx_settings['aff_payout_percents2'] > 90)
      $mwx_settings['aff_payout_percents2'] = "90";

   if ($mwx_settings['aff_payout_percents3'] > 90)
      $mwx_settings['aff_payout_percents3'] = "90";

   if ($mwx_settings['aff_tiers_num'] < 1)
      $mwx_settings['aff_tiers_num'] = "1";
   else if ($mwx_settings['aff_tiers_num'] > 5)
      $mwx_settings['aff_tiers_num'] = "5";
   //---------------------------------------

   update_option ('MemberWing-X', $mwx_settings);

   if (isset($_POST['memberwing-x-license_code']))
      MWX__Validate_License ($_POST['memberwing-x-license_code']);
   else
      MWX__Validate_License ($mwx_settings['memberwing-x-license_code']);
}
//===========================================================================

//===========================================================================
function MWX__reset_settings ()
{
   global   $g_MWX__config_defaults;

   update_option ('MemberWing-X', $g_MWX__config_defaults);

   MWX__Validate_License ($g_MWX__config_defaults['memberwing-x-license_code']);
}
//===========================================================================

//===========================================================================
function MWX__show_user_profile ($user_info=FALSE)
{
   $mwx_settings = MWX__get_settings();

   $user_id = FALSE;
   if ($user_info && is_object($user_info))
      $user_id = $user_info->ID;
   else
      {
      if (isset($_REQUEST['user_id']))
         $user_id = $_REQUEST['user_id'];

      if ($user_id === FALSE)
         {
         global $current_user;
         get_currentuserinfo();
         $user_id = $current_user->ID;
         }
      }

   if ($user_id === FALSE)
      {
      echo '<div align="center" style="margin:20px;padding:10px;border:2px solid red;">Cannot determine user_id. Time to upgrade Wordpress?</div>';
      return;
      }

   $products_table_html = MWX__GetProductsTableHTML            ($user_id);
   $aff_info_table_html = MWX__GetAffiliateInfoTableTableHTML  ($user_id);

   echo '<div align="center" style="border:2px solid #F88;margin:10px 0;padding:4px;background-color:#FFE;"><span style="font-size:140%;color:#21759B;"><span style="font-weight:bold;">M</span>ember<span style="font-weight:bold;">W</span>ing-<span style="color:#F22;font-weight:bold;">X</span></span><div style="margin-top:4px;font-weight:bold;font-size:100%;background-color:#EEE;">User Products and Affiliate Information</div></div>';
   echo $products_table_html;

   if ($mwx_settings['mwx_affiliate_network_enabled'])
      echo $aff_info_table_html;
}
//===========================================================================

//===========================================================================
function MWX__update_user_profile ($user_id=FALSE)
{
   if (!current_user_can ('edit_users'))
       return FALSE;

   if (!$user_id)
      {
      if (isset($_REQUEST['user_id']))
         $user_id = $_REQUEST['user_id'];
      }
   if ($user_id === FALSE)
      {
      global $current_user;
      get_currentuserinfo();
      $user_id = $current_user->ID;
      }
   if ($user_id === FALSE)
      {
      echo '<div align="center" style="margin:20px;padding:10px;border:2px solid red;">Cannot determine user_id. Time to upgrade Wordpress?</div>';
      return;
      }

   //---------------------------------------
   // Update purchases information
   $products_purchased = maybe_unserialize(get_usermeta ($user_id, 'mwx_purchases'));

   if (isset($_POST['products']) && is_array ($_POST['products']))
      {
      foreach ($_POST['products'] as $prod_idx=>$prod_inputs)
         {
         if (isset($prod_inputs['delete']) && $prod_inputs['delete'])
            {
            unset ($products_purchased[$prod_idx]);
            }
         else
            {
            $products_purchased[$prod_idx]['product_status'] = $prod_inputs['product_status'];
            }
         }

      $products_purchased = array_values ($products_purchased);   // Reindex array.
      }

   if (isset($_POST['new_product']) && is_array($_POST['new_product']) && ($_POST['new_product']['product_id'] || $_POST['new_product']['product_name']))
      {
      // Adding new product
      $products_purchased[] =
         array (
            'product_id'      => $_POST['new_product']['product_id'],
            'product_name'    => $_POST['new_product']['product_name'],
            'date'            => date ('Y-m-d H:i:s', strtotime ("now")),
            'txn_ids'         => array("Manual by admin"),
            'subscr_id'       => $_POST['new_product']['subscr_id'],
            'product_status'  => 'active',      // 'active'(customer is in good standing), 'cancelled'(subscription), 'refunded'(one of payments was refunded), 'deactivated'(if refund happened or manually set by admin)
            );
      }

   update_usermeta ($user_id, 'mwx_purchases', serialize ($products_purchased));
   //---------------------------------------

   //---------------------------------------
   // Update affiliate information
   $mwx_aff_info = maybe_unserialize (get_usermeta ($user_id, 'mwx_aff_info'));
   if (isset($mwx_aff_info['aff_status']))
      {
      if (isset($_POST['aff_status']))
         $mwx_aff_info['aff_status'] = $_POST['aff_status'];
      if (isset($_POST['immune_to_min_payout_limit']))
         $mwx_aff_info['immune_to_min_payout_limit'] = $_POST['immune_to_min_payout_limit'];
      if (isset($_POST['payout_percents']))
         $mwx_aff_info['payout_percents'] = $_POST['payout_percents'];
      if (isset($_POST['payout_adjustment']))
         $mwx_aff_info['payout_adjustment'] = $_POST['payout_adjustment'];

      if (isset($_POST['aff_sale_status']) && is_array($_POST['aff_sale_status']))
         {
         foreach ($_POST['aff_sale_status'] as $ref_index=>$referral_sale_status)
            $mwx_aff_info['referrals'][$ref_index]['status'] = $referral_sale_status;
         }

      if (isset($_POST['aff_payout']) && is_array($_POST['aff_payout']))
         {
         foreach ($_POST['aff_payout'] as $ref_index=>$referral_aff_payout)
            $mwx_aff_info['referrals'][$ref_index]['payout_amt'] = $referral_aff_payout;
         }

      // Must be last
      if (isset($_POST['delete_aff_referral']) && is_array($_POST['delete_aff_referral']))
         {
         foreach ($_POST['delete_aff_referral'] as $ref_index=>$v)
            {
            unset ($mwx_aff_info['referrals'][$ref_index]);  // Remove element from array.
            }

         $mwx_aff_info['referrals'] = array_values ($mwx_aff_info['referrals']);   // Reindex array.
         }

      update_usermeta ($user_id, 'mwx_aff_info', serialize ($mwx_aff_info));
      }
   //---------------------------------------
}
//===========================================================================

//===========================================================================
//
// Returns subset of settings related to affiliate network
function MWX__GetAffiliateNetworkSettings ($mwx_settings = FALSE)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings();

   $mwx_aff_settings = array();

   $mwx_aff_settings['mwx_affiliate_network_enabled'] = $mwx_settings['mwx_affiliate_network_enabled'];

   foreach ($mwx_settings as $k=>$v)
      {
      if (strpos ($k, "aff_") === 0)
         $mwx_aff_settings[$k] = $v;
      }

   $mwx_aff_settings['system_info'] =
      array (
         'wordpress_version'  => get_bloginfo('version'),
         'mwx_version'        => MEMBERWING_X_VERSION,
         'mwx_edition'        => MEMBERWING_X_EDITION,
         'license_code'       => $mwx_settings['memberwing-x-license_code'],
         'license_domain'     => $_SERVER['HTTP_HOST'],
         'admin_email'        => MWX__Get_Admin_Email(),
         );

   return ($mwx_aff_settings);
}
//===========================================================================

//===========================================================================
//
// Adds table with the list of products owned by this user to user's profile.

function MWX__GetProductsTableHTML ($user_id)
{
   // array (array('product_id'=>'5', 'product_name'=>'', 'date'=>'2009-12-02', 'txn_id'=>array(...), 'subscr_id'=>'', 'active'=>'1'), array(...), ...)
   $products_purchased = maybe_unserialize(get_usermeta ($user_id, 'mwx_purchases'));

//------------------------------------------
// Admin view of products table
$table_html_admin =<<<TTT
<table style="background-color:#555;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;" colspan="5"><div align="center">Products owned</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="5%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Product ID<br />Article ID</strong></div></td>
      <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Subscr ID</strong></div></td>
      <td style="background-color:#B5FFA8;" width="65%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Product name</strong></div></td>
      <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Status?</strong></div></td>
      <td style="background-color:#FF9D9F;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; font-weight: bold;">Delete!</div></td>
   </tr>
   {TABLE_ROWS}
  <tr>
    <td  style="background-color:#FBFFB3;"colspan="5"><div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 12px;padding:6px 0;"><strong>Add new product for this user:</strong></div></td>
  </tr>
  <tr>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[product_id]"   value="" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[subscr_id]"   value="" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[product_name]" value="" class="regular-text" size="100" /></div></td>
      <td colspan="2" style="background-color:gray;"><div align="center"></div></td>
  </tr>
</table>
TTT;

$product_row_admin =<<<TTT
   <tr>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_id]" value="{PRODUCT_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][subscr_id]" value="{SUBSCR_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_name]" value="{PRODUCT_NAME}" readonly="readonly" class="regular-text" size="100"/></div></td>
      <td style="background-color:white;"><div align="center">{PRODUCT_STATUS_SELECT_HTML}</div></td>
      <td style="background-color:white;"><div align="center"><input type="checkbox" value="1" name="products[{PROD_IDX}][delete]" /></div></td>
   </tr>
TTT;
//------------------------------------------

//------------------------------------------
// User view of products table
$table_html_user =<<<TTT
<table style="background-color:#555;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;" colspan="4"><div align="center">MemberWing-X<br />Products owned</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Product ID<br />Article ID</strong></div></td>
      <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Subscr ID</strong></div></td>
      <td style="background-color:#B5FFA8;" width="90%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Product name</strong></div></td>
   </tr>
   {TABLE_ROWS}
</table>
TTT;

$product_row_user =<<<TTT
   <tr>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_id]" value="{PRODUCT_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][subscr_id]" value="{SUBSCR_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_name]" value="{PRODUCT_NAME}" readonly="readonly" class="regular-text" size="100"/></div></td>
   </tr>
TTT;
//------------------------------------------

$no_products_row = '<tr><td style="background-color:#FBFFB3;" colspan="5"><div align="center" style="font-family: Georgia, Times, serif;font-size: 12px;padding:6px 0;"><strong>No products currently owned</strong></div></td></tr>';

   if (current_user_can ('edit_users'))
      {
      $table_html  = $table_html_admin;
      $product_row = $product_row_admin;
      $is_admin = TRUE;
      }
   else
      {
      $table_html  = $table_html_user;
      $product_row = $product_row_user;
      $is_admin = FALSE;
      }

   if (is_array($products_purchased) && count($products_purchased))
      {
      $product_rows="";
      foreach ($products_purchased as $idx=>$product)
         {
         if (!$is_admin && $product['product_status'] != 'active')
            continue; // Do not show inactive products to regular users.

         $next_row = $product_row;
         $next_row = preg_replace ('@\{PROD_IDX\}@',           $idx, $next_row);
         $next_row = preg_replace ('@\{PRODUCT_ID\}@',         $product['product_id'], $next_row);
         $next_row = preg_replace ('@\{SUBSCR_ID\}@',          $product['subscr_id'], $next_row);
         $next_row = preg_replace ('@\{PRODUCT_NAME\}@',       $product['product_name'], $next_row);

         //------------------------------------
         // Calc product status HTML
         // 'active'(customer is in good standing), 'cancelled'(subscription), 'ended'(subscription ended normally), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
         $sel_msg = array('active'=>'', 'cancelled'=>'', 'ended'=>'', 'refunded'=>'', 'deactivated'=>'');
         $sel_msg[$product['product_status']] = 'selected="selected"';
         $product_status_html =<<<TTT
<select name="products[$idx][product_status]" size="1">
           <option value="active"      {$sel_msg['active']}>active</option>
           <option value="cancelled"   {$sel_msg['cancelled']}>cancelled</option>
           <option value="ended"       {$sel_msg['ended']}>ended</option>
           <option value="refunded"    {$sel_msg['refunded']}>refunded</option>
           <option value="deactivated" {$sel_msg['deactivated']}>deactivated</option>
</select>
TTT;
         //------------------------------------

         $next_row = preg_replace ('@\{PRODUCT_STATUS_SELECT_HTML\}@', $product_status_html, $next_row);
         $product_rows .= $next_row;
         }
      $table_html = preg_replace ('@\{TABLE_ROWS\}@', $product_rows, $table_html);
      }
   else
      {
      $table_html = preg_replace ('@\{TABLE_ROWS\}@', $no_products_row, $table_html);
      }

   return $table_html;
}
//===========================================================================

//===========================================================================
function MWX__GetAffiliateInfoTableTableHTML ($user_id)
{
//   if ($user->user_level==10)
//      return '';
   $mwx_settings = get_option ('MemberWing-X');

   // Get information about user as affiliate
   $mwx_aff_info = maybe_unserialize (get_usermeta ($user_id, 'mwx_aff_info'));

   // ***********************************************************************
   // ***** Build "affiliate links" information table ***********************
   // ***********************************************************************
   $aff_tracking_code = '<span style="background:#FFA;padding:0 2px;"><b>?' . $mwx_settings['aff_affiliate_id_url_prefix'] . "=" . $user_id . '</b></span>';
   $aff_url_sample1 = '&nbsp;&nbsp;&nbsp;<span style="font-size:120%;">' . rtrim(get_bloginfo ('wpurl'), '/')  . "/<b>?" . $mwx_settings['aff_affiliate_id_url_prefix'] . "=" . $user_id . '</b>' . '</span>';
   $aff_url_sample2 = '&nbsp;&nbsp;&nbsp;<span style="font-size:120%;">' . rtrim(get_bloginfo ('wpurl'), '/')  . "/any-page/<b>?" . $mwx_settings['aff_affiliate_id_url_prefix'] . "=" . $user_id . '</b>' . '</span>';

   $aff_info_html   = '<div style="margin:10px 0;padding:0;border:2px solid gray;">';
   $aff_info_html  .=   '<div align="center" style="margin:0;padding:4px;background-color:#DDD;border-bottom:2px solid gray;font-weight:bold;">Affiliate links';
   $aff_info_html  .=   '</div>';
   $aff_info_html  .=   '<div style="margin:4px;padding:4px;background-color:#FFF;">';
   $aff_info_html  .=   "Your affiliate ID is: <b>{$user_id}</b>. Use it to build your affiliate links to any page of this site, such as:<br />$aff_url_sample1<br />or deep links (links to any page of your choice). Just append to any URL your affiliate tracking code: $aff_tracking_code to be credited for every sale you refer like this:<br />$aff_url_sample2";
   $aff_info_html  .=   '</div>';
   $aff_info_html  .= '</div>';

   if (!current_user_can ('edit_users'))
      {
      return $aff_info_html;  ///!!! no aff info yet for non-admins
      }
   // ***********************************************************************


   // ***********************************************************************
   // ***** Build "affiliate account information" table *********************
   // ***********************************************************************
   if (!isset($mwx_aff_info['aff_status']))
      $aff_account_info_table_html_admin = "";
   else
      {
      //------------------------------------
      // Calc aff account status HTML
      $sel_msg = array('active'=>'', 'pending'=>'', 'declined'=>'', 'banned'=>'');
      $sel_msg[$mwx_aff_info['aff_status']] = 'selected="selected"';
      $aff_account_status = $mwx_aff_info['aff_status'];
      $aff_account_status_html =<<<TTT
<select name="aff_status" size="1">
        <option value="active"   {$sel_msg['active']}>active</option>
        <option value="pending"  {$sel_msg['pending']}>pending</option>
        <option value="declined" {$sel_msg['declined']}>declined</option>
        <option value="banned"   {$sel_msg['banned']}>banned</option>
</select>
TTT;
      //------------------------------------
      //------------------------------------
      // Calc immune HTML
      if ($mwx_aff_info['immune_to_min_payout_limit'])
         $aff_immune_html = '<input type="hidden" name="immune_to_min_payout_limit" value="0" /><input type="checkbox" value="1" name="immune_to_min_payout_limit" checked="checked" />';
      else
         $aff_immune_html = '<input type="hidden" name="immune_to_min_payout_limit" value="0" /><input type="checkbox" value="1" name="immune_to_min_payout_limit" />';
      //------------------------------------
      //------------------------------------
      // Calc payout % HTML
      $payout_percents_html = '<input type="text" name="payout_percents" value="' . max ($mwx_aff_info['payout_percents'], $mwx_settings['aff_payout_percents']) . '" size="3" />%';
      //------------------------------------
      //------------------------------------
      // Calc Payout adjustment
      if ($mwx_aff_info['payout_adjustment'] < 0)
         $dollar_sign = '<span style="color:red;font-weight:bold;">$</span>';
      else if ($mwx_aff_info['payout_adjustment'] == 0)
         $dollar_sign = '<span style="color:black;font-weight:bold;">$</span>';
      else
         $dollar_sign = '<span style="color:black;font-weight:green;">$</span>';

      $payout_adjustment_html = $dollar_sign . '<input type="text" name="payout_adjustment" value="' . ($mwx_aff_info['payout_adjustment']?$mwx_aff_info['payout_adjustment']:"0.00") . '" size="7" />';
      //------------------------------------
      //------------------------------------
      // Calc Notes
      $notes_html = $mwx_aff_info['sandbox_account']?"Sandbox":"";
      //------------------------------------

      $aff_account_info_table_html_admin =<<<TTT
<table style="background-color:#555;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;" colspan="5"><div align="center">Affiliate account information</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="20%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Affiliate account status</strong></div></td>
      <td style="background-color:#B5FFA8;" width="20%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Immune to minimal payout limit?</strong></div></td>
      <td style="background-color:#B5FFA8;" width="20%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout % - off total sale</strong></div></td>
      <td style="background-color:#B5FFA8;" width="20%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout adjustment<br /><span style="font-size:75%;">(to be processed during the next payout)</span></strong></div></td>
      <td style="background-color:#B5FFA8;" width="20%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Notes</strong></div></td>
   </tr>
   <tr>
      <td style="background-color:white;"><div align="center">$aff_account_status_html</div></td>
      <td style="background-color:white;"><div align="center">$aff_immune_html</div></td>
      <td style="background-color:white;"><div align="center">{$payout_percents_html}</div></td>
      <td style="background-color:white;"><div align="center">{$payout_adjustment_html}</div></td>
      <td style="background-color:white;"><div align="center">$notes_html</div></td>
   </tr>
</table>
TTT;
      }
   // ***********************************************************************


   // ***********************************************************************
   // ***** Build "Sales Referred" table ************************************
   // ***********************************************************************
   if (!isset($mwx_aff_info['referrals']))
      $aff_sales_referred_table_html_admin = "";
   else
      {
      $referral_rows_html = '';
      foreach ($mwx_aff_info['referrals'] as $ref_index=>$referral)
         {
         $referral_row_html  = '<tr>';
         $referral_row_html .= '<td style="background-color:white;"><div align="center">' . $referral['txn_date'] . '</div></td>';
         $referral_row_html .= '<td style="background-color:white;"><div align="center">' . $referral['txn_id'] . '</div></td>';
         $referral_row_html .= '<td style="background-color:white;"><div align="center">$ ' . $referral['full_sale_amt'] . '</div></td>';

         // Already processed aff payout cannot be editable.
         // $aff_payout_html = '<input type="text" name="aff_payout[' . $ref_index . ']" value="' . $referral['payout_amt'] . '" size="7" />';
         $aff_payout_html = $referral['payout_amt'];
         $referral_row_html .= '<td style="background-color:white;"><div align="center">$ ' . $aff_payout_html . '</div></td>';

         $tier_html          = isset($referral['affiliate_tier'])?$referral['affiliate_tier']:'1';
         $referral_row_html .= '<td style="background-color:white;"><div align="center">' . $tier_html . '</div></td>';

         if (is_numeric($referral['referral_for_id']))
            {
            $user_data = get_userdata ($referral['referral_for_id']);
            if ($user_data)
               $customer = $user_data->user_email;
            else
               $customer = $referral['referral_for_id'];
            }
         else
            $customer = $referral['referral_for_id'];
         $referral_row_html .= '<td style="background-color:white;"><div align="center">' . $customer . '</div></td>';

         //------------------------------------
         // Calc aff sale status status HTML
         $aff_sale_status = array('approved'=>'', 'declined'=>'', 'refunded'=>'', 'reversed'=>'', 'pending'=>'', 'adjusted'=>'');
         $aff_sale_status[$referral['status']] = 'selected="selected"';
         $aff_sale_status_html =<<<TTT
<select name="aff_sale_status[$ref_index]" size="1">
           <option value="pending"  {$aff_sale_status['pending']}>pending</option>
           <option value="approved" {$aff_sale_status['approved']}>approved</option>
           <option value="declined" {$aff_sale_status['declined']}>declined</option>
           <option value="refunded" {$aff_sale_status['refunded']}>refunded</option>
           <option value="reversed" {$aff_sale_status['reversed']}>reversed</option>
           <option value="adjusted" {$aff_sale_status['adjusted']}>adjusted</option>
</select>
TTT;
         //------------------------------------

         $referral_row_html .= '<td style="background-color:white;"><div align="center">' . $aff_sale_status_html . '</div></td>';

         if ($referral['paid']) $paid='Yes'; else $paid='No';
         $referral_row_html .= '<td style="background-color:white;"><div align="center">' . $paid . '</div></td>';
         // Deleting already existing referral cause balance calculation problems.
         // $referral_row_html .= '<td style="background-color:white;"><div align="center"><input type="checkbox" value="1" name="delete_aff_referral[' . $ref_index . ']" /></div></td>';
         $referral_row_html .= '</tr>';

         $referral_rows_html .= $referral_row_html;
         }


      $aff_sales_referred_table_html_admin =<<<TTT
<table style="background-color:#555;margin-top:10px;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;" colspan="9"><div align="center">Sales referred</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="13%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Txn Date</strong></div></td>
      <td style="background-color:#B5FFA8;" width="17%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Txn ID</strong></div></td>
      <td style="background-color:#B5FFA8;" width="13%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Full Sale Amt</strong></div></td>
      <td style="background-color:#B5FFA8;" width="13%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Aff Payout</strong></div></td>
      <td style="background-color:#B5FFA8;" width="5%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Tier</strong></div></td>
      <td style="background-color:#B5FFA8;" width="17%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Buyer/Customer</strong></div></td>
      <td style="background-color:#B5FFA8;" width="13%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Aff Sale Status</strong></div></td>
      <td style="background-color:#B5FFA8;" width="9%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Instantly Paid?</strong></div></td>
      <!--   <td style="background-color:#FF9D9F;" width="5%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; font-weight: bold;">Delete!</div></td>  -->
   </tr>
   $referral_rows_html
</table>
TTT;
      }
   // ***********************************************************************

   // ***********************************************************************
   // ***** Build "Payouts Processed" table *********************************
   // ***********************************************************************

   if (!isset($mwx_aff_info['payouts']))
      $aff_payouts_processed_table_html_admin = "";
   else
      {
      $payouts_rows_html = '';
      foreach ($mwx_aff_info['payouts'] as $payout_index=>$payout)
         {
         $payouts_row_html  = '<tr>';
         $payouts_row_html .= '<td style="background-color:white;"><div align="center">'   . $payout['date']                        . '</div></td>';
         $payouts_row_html .= '<td style="background-color:white;"><div align="center">'   . $payout['payout_txn_id']               . '</div></td>';
         $payouts_row_html .= '<td style="background-color:white;"><div align="center">$ ' . $payout['payout_amt']                  . '</div></td>';
         if ($payout['payout_adjustment_included'] >= 0)
            $payouts_row_html .= '<td style="background-color:white;"><div align="center">$ ' . $payout['payout_adjustment_included']  . '</div></td>';
         else
            $payouts_row_html .= '<td style="background-color:white;"><div align="center"><span style="color:red;">-$ ' . -$payout['payout_adjustment_included']  . '</span></div></td>';
         $payouts_row_html .= '</tr>';

         $payouts_rows_html .= $payouts_row_html;
         }


      $aff_payouts_processed_table_html_admin =<<<TTT
<table style="background-color:#555;margin-top:10px;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;" colspan="8"><div align="center">Payouts Processed</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="25%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout Date</strong></div></td>
      <td style="background-color:#B5FFA8;" width="25%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout Txn ID</strong></div></td>
      <td style="background-color:#B5FFA8;" width="25%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout Amount</strong></div></td>
      <td style="background-color:#B5FFA8;" width="25%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout Adjustment Included</strong></div></td>
   </tr>
   $payouts_rows_html
</table>
TTT;
      }
   // ***********************************************************************

   return $aff_info_html . $aff_account_info_table_html_admin . $aff_sales_referred_table_html_admin . $aff_payouts_processed_table_html_admin;
}
//===========================================================================

//===========================================================================
// This function runs upon plugin activation or when webmaster pressed "Update settings" or "Validate License" button.
// If license is valid - returns array of license information for the given 'license_code'. Otherwise returns FALSE.

function MWX__Validate_License ($license_code)
{
   global   $g_MWX__config_defaults;

   // Load current settings
   $mwx_settings = MWX__get_settings ();
   $mwx_settings['memberwing-x-license-valid'] = "0";

   if (trim($mwx_settings['your-memberwing-affiliate-link']))
      $mwx_settings['brd'] = str_replace ('{{{MEMBERWING_URL}}}', trim($mwx_settings['your-memberwing-affiliate-link']), $g_MWX__config_defaults['brd']);
   else
      $mwx_settings['brd'] = str_replace ('{{{MEMBERWING_URL}}}', 'http://www.memberwing.com/', $g_MWX__config_defaults['brd']);

   // Get fresh info
   $license_code = trim($license_code);
   $license_data = trim(@file_get_contents ("http://www.memberwing.com/LICENSE_VALIDATOR/?mwx_version=" . MEMBERWING_X_VERSION . "&mwx_edition=" . MEMBERWING_X_EDITION . "&license_code=$license_code&license_domain={$_SERVER['HTTP_HOST']}&admin_email=" . MWX__Get_Admin_Email()));

   $LV__license_info = @unserialize(trim($license_data));

   if (!isset($LV__license_info) || !is_array($LV__license_info))
      $LV__license_info = array ('license_status'=>'undefined', 'message'=>'Cannot contact host');

   if (isset($LV__license_info['license_status']) && $LV__license_info['license_status']   == 'valid')
      $mwx_settings['memberwing-x-license-valid'] = "1";

   if (isset($LV__license_info['brd']))
      {
      if (trim($mwx_settings['your-memberwing-affiliate-link']))
         $mwx_settings['brd'] = str_replace ('{{{MEMBERWING_URL}}}', trim($mwx_settings['your-memberwing-affiliate-link']), $LV__license_info['brd']);
      else
         $mwx_settings['brd'] = str_replace ('{{{MEMBERWING_URL}}}', 'http://www.memberwing.com/', $LV__license_info['brd']);
      }

   MWX__update_settings ($mwx_settings);
   return ($LV__license_info);
}
//===========================================================================

//===========================================================================
function MWX__render_admin_page_html ($admin_page_name)
{
   echo '<div style="margin-top:10px;padding-right:20px;">';

   MWX__render_memberwing_x_version ();

   switch ($admin_page_name)
      {
      case 'general'    :           MWX__render_general_settings_page_html();          break;
      case 'paypal'     :           MWX__render_paypal_settings_page_html();           break;
      case 'autoresponders'     :   MWX__render_autoresponders_settings_page_html();   break;
      case 'email'     :            MWX__render_email_settings_page_html();            break;
      case 'affiliate settings'  :  MWX__render_affiliate_settings_page_html();        break;
      case 'affiliate payouts'  :   MWX__render_affiliate_payouts_page_html();         break;
      case 'other systems'     :    MWX__render_other_systems_settings_page_html();    break;
      }

   echo '</div>';
}
//===========================================================================

//===========================================================================
function MWX__render_memberwing_x_version ()
{
   $latest_available_version = file_get_contents ('http://www.memberwing.com/LATEST/mwx/get.php?what=latest_version_number');
?>
   <div align="center" style="border:2px solid gray;font-size:130%;margin-top:10px;padding:5px;">MemberWing-X Version: <span style="color:red;font-weight:bold;">
      <?php echo MEMBERWING_X_VERSION . ' ' . MEMBERWING_X_EDITION; ?></span>
      <div style="height:3px;"></div>
      <span style="font-size:75%;font-weight:bold;background-color:#DDD;">&nbsp;&nbsp;<?php echo 'Your Wordpress version: ' . get_bloginfo('version'); ?>&nbsp;&nbsp;</span>

<?php if (defined('MEMBERWING_VERSION')) : ?>
      <div style="margin-top:3px;font-size:75%;font-weight:bold;background-color:#FFA;color:red;border:1px solid red;padding:2px;">&nbsp;&nbsp;Warning: You have legacy MemberWing 4.x plugin activated. It is strongly advised to deactivate it when MemberWing-X is active to avoid conflicts.&nbsp;&nbsp;</div>
<?php endif; ?>

<?php if (get_bloginfo('version') < 2.8) : ?>
      <div style="margin-top:3px;font-size:75%;font-weight:bold;background-color:#FFA;color:red;border:1px solid red;padding:2px;">&nbsp;&nbsp;Warning: You have an old version of Wordpress active. Gradual Content Delivery (dripping content) functionality is only available for Wordpress 2.8 and higher. Please upgrade.&nbsp;&nbsp;</div>
<?php endif; ?>

<?php if (MEMBERWING_X_VERSION < $latest_available_version) : ?>
      <div style="margin-top:3px;font-size:75%;font-weight:bold;background-color:#FFA;color:red;border:1px solid red;padding:2px;">&nbsp;&nbsp;Alert: newer version of MemberWing-X is available: <span style="font-size:125%;"><?php echo $latest_available_version; ?></span>.&nbsp;&nbsp;&nbsp;<a href="http://www.memberwing.com/LATEST/mwx/get.php?what=zip&version=latest">Please download and upgrade!</a>&nbsp;&nbsp;</div>
<?php endif; ?>

   </div>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_general_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();

   $premium_content_warning_premium_html = MWX__AssemblePremiumContentWarningMessage ("12", "0", "4.95|gold");
   $premium_content_warning_free_html    = MWX__AssemblePremiumContentWarningMessage ("12", "0", "*");

   if (!$mwx_settings['memberwing-x-license_code'])
      $license_message = 'Your license code for MemberWing-X<br /><b>Users who own MemberWing-X license are eligible for technical support and have an ability to turn off branding messages.</b><br /><div align="center" style="padding:3px;"><a href="http://www.memberwing.com/"><span align="center" style="font-weight:bold;font-size:130%;">Buy MemberWing-X License here</span></a></div>';
   else if ($mwx_settings['memberwing-x-license-valid'])
      $license_message = 'Your license code for MemberWing-X<br /><div align="center" style="border:2px solid green;margin:2px;padding:4px;">License is valid</div>';
   else
      $license_message = 'Your license code for MemberWing-X<br /><div align="center" style="border:2px solid red;background-color:yellow;margin:2px;padding:4px;">This license code is invalid</div><div align="center" style="padding:3px;"><a href="http://www.memberwing.com/"><span align="center" style="font-weight:bold;font-size:130%;">Buy MemberWing-X License here</span></a><div>';

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">General Settings</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="45%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="35%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">MemberWing-X License Code:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <input type="text" name="memberwing-x-license_code" value="<?php echo $mwx_settings['memberwing-x-license_code']; ?>" size="80" />
               <div align="center"><input style="padding:4px;margin-top:4px;background-color:#BEB;font-weight:bold;" type="submit" name="validate_memberwing-x-license" value="Validate MemberWing-X License" /></div>
               <div style="<?php if (!$mwx_settings['memberwing-x-license-valid']) echo 'display:none;'; ?>">
                  Show &quot;Powered by MemberWing-X&quot;: <input type="hidden" name="show-powered-by" value="0" /><input type="checkbox" value="1" name="show-powered-by" <?php if ($mwx_settings['show-powered-by']) echo ' checked="checked" '; ?> />
               </div>
            </div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;"><?php echo $license_message; ?></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Your memberwing affiliate link:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="your-memberwing-affiliate-link" value="<?php echo $mwx_settings['your-memberwing-affiliate-link']; ?>" size="70" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your memberwing affiliate link. Example: <b>http://www.memberwing.com/270.html</b><br />If you put your affiliate link here then "Powered by" message link will use it. This will help you to earn affiliate commissions if someone will buy MemberWing by clicking on your affiliate "Powered by" link.<br />If you don't have affiliate link yet, <a href="http://www.memberwing.com/affiliate-program/" target="_blank"><b>please signup here</b></a>. </div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Keep access to ended subscriptions?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="keep_access_for_ended_subscriptions" value="0" /><input type="checkbox" value="1" name="keep_access_for_ended_subscriptions" <?php if ($mwx_settings['keep_access_for_ended_subscriptions']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: When subscription ended normally (end of term) user can still access premium subscription-based content, Disabled: when subscription ended normally - user will be denied access to premium content.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Payment success page:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="payment_success_page" value="<?php echo $mwx_settings['payment_success_page']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Page URL where user will be redirected after successful payment. It is good idea to put on this page instructions for user to check his email and makes sure your email passes his ISP spam filters.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Payment cancel page:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="payment_cancel_page" value="<?php echo $mwx_settings['payment_cancel_page']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Page URL where user will be redirected after payment is cancelled. It is good idea to ask user for feedback about cancellation and direct him to your contact form.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">&quot;Buy Now&quot; button image URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="buy_now_button_image" value="<?php echo $mwx_settings['buy_now_button_image']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;"><img src="<?php echo $mwx_settings['buy_now_button_image']; ?>" /> URL of your custom &quot;Buy Now&quot; image</div></td>
      </tr>
<!--
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">&quot;Add to cart&quot; button image URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="add_to_cart_button_image" value="<?php echo $mwx_settings['add_to_cart_button_image']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;"><img src="<?php echo $mwx_settings['add_to_cart_button_image']; ?>" /> URL of your custom &quot;Add to cart&quot; image</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">&quot;View Cart&quot; button image URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="view_cart_button_image" value="<?php echo $mwx_settings['view_cart_button_image']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;"><img src="<?php echo $mwx_settings['view_cart_button_image']; ?>" /> URL of your custom &quot;View Cart&quot; image</div></td>
      </tr>
-->
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{LOGIN_MSG}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="login_msg" cols=90 rows=2><?php echo $mwx_settings['login_msg']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {LOGIN_MSG} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{SUBSCRIBE_URL_PREMIUM}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <input type="text" name="subscribe_url_premium" value="<?php echo $mwx_settings['subscribe_url_premium']; ?>" size="80" />
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {SUBSCRIBE_URL_PREMIUM} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{SUBSCRIBE_MSG_PREMIUM}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="subscribe_msg_premium" cols=90 rows=2><?php echo $mwx_settings['subscribe_msg_premium']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {SUBSCRIBE_MSG_PREMIUM} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{SUBSCRIBE_URL_FREE}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <input type="text" name="subscribe_url_free" value="<?php echo $mwx_settings['subscribe_url_free']; ?>" size="80" />
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {SUBSCRIBE_URL_FREE} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{SUBSCRIBE_MSG_FREE}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="subscribe_msg_free" cols=90 rows=2><?php echo $mwx_settings['subscribe_msg_free']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {SUBSCRIBE_MSG_FREE} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{PROMO_MSG_PREMIUM}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="promo_msg_premium" cols=90 rows=2><?php echo $mwx_settings['promo_msg_premium']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {PROMO_MSG_PREMIUM} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{PROMO_MSG_FREE}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="promo_msg_free" cols=90 rows=2><?php echo $mwx_settings['promo_msg_free']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {PROMO_MSG_FREE} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{BUY_MSG}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="buy_msg" cols=90 rows=2><?php echo $mwx_settings['buy_msg']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {BUY_MSG} variable.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Premium content warning message:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="premium_content_warning" cols=90 rows=5><?php echo $mwx_settings['premium_content_warning']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Warning message (HTML) to be shown to visitors who are not authorized to view premium contents.<br /><u>Paid membership example:</u><br /><?php echo $premium_content_warning_premium_html; ?><u>Free membership example:</u><br /><?php echo $premium_content_warning_free_html; ?></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Welcome email subject:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="welcome_email_subject" value="<?php echo $mwx_settings['welcome_email_subject']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Subject of email to sent to new customer</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Welcome email body:</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="font-size:10px;" name="welcome_email_body" cols=90 rows=5><?php echo $mwx_settings['welcome_email_body']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Body of welcome email to be sent to new customer. Variables in curly brackets, such as: {FIRST_NAME} {LAST_NAME} and others will be substituted with their respective values from customer payment details.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Delete emptyhanded user?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="delete_emptyhanded_user" value="0" /><input type="checkbox" value="1" name="delete_emptyhanded_user" <?php if ($mwx_settings['delete_emptyhanded_user']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled (not recommended): when member does not own any products (after receiving a refund or cancelling subscription) his account will be automatically deleted from the blog.<br />Disabled (recommended): customer who no longer owns any products (after receving a refund or cancelling subscription) will be downgraded to regular subscriber but will remain a member of blog without access to any premium content.<br />Note:<br />- Because every member of your site is also automatically your affiliate - deleting him will erase all information about his referrals, payouts and balances. Use with caution!<br />- If administrator manually deleted all customer's products - customer's account will not be auto-deleted even if this setting is enabled.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Hide comments from non-logged on users?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="hide_comments_from_non_logged_on_users" value="0" /><input type="checkbox" value="1" name="hide_comments_from_non_logged_on_users" <?php if ($mwx_settings['hide_comments_from_non_logged_on_users']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: visitors will not see comments for articles/pages until they will log in.<br />Disabled: Comments for articles/pages will always be visible</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Enable MemberWing legacy compatibility mode?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="memberwing_legacy_compatibility_mode" value="0" /><input type="checkbox" value="1" name="memberwing_legacy_compatibility_mode" <?php if ($mwx_settings['memberwing_legacy_compatibility_mode']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: MemberWing-X will recognize legacy-style premium markers: <b>{+} {++} {+++} {++++}</b> used in MemberWing 2.x-4.x. <b>Note</b>: this will make it a bit slower.<br />Disabled: ignores old-style markers</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Admin acts like regular non-logged on visitor?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="admin_acts_like_regular_visitor" value="0" /><input type="checkbox" value="1" name="admin_acts_like_regular_visitor" <?php if ($mwx_settings['admin_acts_like_regular_visitor']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: admin will not see premium content if it is premium. Useful for testing without need to logoff or switch browsers.<br />Disabled: all-powered admin like yourself sees everything.<br />Notes:<br />- You may add/remove/disable/enable products for members in your admin panel (Admin->Users) and see exactly what regular visitor, product owners and subscribers will see without need to logoff or changing browsers</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">MemberWing-X API endpoint (URL):</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="mwx_api_endpoint" value="<?php echo $mwx_settings['mwx_api_endpoint']; ?>" size="80" readonly="readonly" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of MemberWing-X API endpoint. Used for integration with other systems</div></td>
      </tr>

      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">MemberWing-X API Key:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="mwx_api_key" value="<?php echo $mwx_settings['mwx_api_key']; ?>" size="80" readonly="readonly" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">API Key is used for integration with other applications and systems.</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_paypal_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Paypal Settings</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Paypal email:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="paypal_email" value="<?php echo $mwx_settings['paypal_email']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your paypal email where payment will be sent.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Paypal currency code:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="paypal_currency_code" value="<?php echo $mwx_settings['paypal_currency_code']; ?>" size="8" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">3-letter code of currency used for Paypal transactions. Ex: USD, GBP, EUR, CAD, AUD, ... etc<br /><a href="https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_wa-outside"><b>Get all Paypal currency codes here</b></a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Paypal buttons integration code</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="font-size:10px;" name="paypal_integration_code_html" cols=90 rows=4 readonly="readonly" onclick="this.select();"><?php echo $mwx_settings['paypal_integration_code_html']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Paypal code to be used when directly editing HTML code of Paypal 'Buy' and 'Subscribe' buttons. This code is to be added right before closing <b>&lt;/form&gt;</b> tag of Paypal button HTML code. This code will allow full integration of your Paypal button with MemberWing-X.<br /><b>Note</b>: please make sure your paypal button does not already have 'ipn_notify' variable pointing to another script.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Automatic Integration Code Insertion for Paypal buttons?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="paypal_integration_code_auto_insert" value="0" /><input type="checkbox" value="1" name="paypal_integration_code_auto_insert" <?php if ($mwx_settings['paypal_integration_code_auto_insert']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: MemberWing-X will automatically insert above code into every Paypal button on your site. This will make every Paypal button on your site integrated with MemberWing-X and it's affiliate system<br />Disabled: You have to manually insert Paypal Button Integration Code (above) into HTML code of every Paypal button to make sure it is integrated with MemberWing-X and it's affiliate system.</div></td>
      </tr>
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:8px 0 3px;"><a href="https://developer.paypal.com/">Paypal Sandbox</a> Settings<br />Used for testing only by advanced integrators</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Paypal Sandbox enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="paypal_sandbox_enabled" value="0" /><input type="checkbox" value="1" name="paypal_sandbox_enabled" <?php if ($mwx_settings['paypal_sandbox_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables <a href="https://developer.paypal.com/">Paypal sandbox</a> for testing. When enabled - all new Paypal transactions will go through sandbox. All new affiliates will be marked as 'sandbox accounts'. You will be able to test buyer, seller and affiliate transactions via your paypal sandbox accounts.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Paypal Sandbox Email (sandbox seller email):</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="paypal_sandbox_email" value="<?php echo $mwx_settings['paypal_sandbox_email']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Paypal Sandbox seller (sandbox merchant) email</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Sandbox tester's computer IP address:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="sandbox_machine_ip_address" value="<?php echo $mwx_settings['sandbox_machine_ip_address']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Optional (advanced testers only): set this to IP address of your local machine to allow debugging using ActiveState Komodo environment</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_autoresponders_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Integration with Autoresponders</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>

      <!-- AWeber Integration -->
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.memberwing.com/goto/aweber" target="_blank">AWeber autoresponder</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">AWeber Integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aweber_integration_enabled" value="0" /><input type="checkbox" value="1" name="aweber_integration_enabled" <?php if ($mwx_settings['aweber_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.memberwing.com/goto/aweber" target="_blank">AWeber autoresponder</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Email address of your AWeber list<br />(YOUR-LISTNAME@aweber.com):</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="aweber_list_email" value="<?php echo $mwx_settings['aweber_list_email']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Memberwing will add new subscriber to the above listname as soon as subscriber paid to join your membership site. He will then receive confirmation email from Aweber. <span style="color: red; background-color: rgb(255, 255, 153);">Please note:</span> You must activate MemberWing parser within your Aweber mailing list configuration panel: My Lists->Email Parser-> [x] <b><i>MemberWing</i></b>. Without this step no new subscribers will be added to your Aweber list. If you need assistance regarding this - please contact Aweber helpdesk: help@aweber.com</div></td>
      </tr>

      <!-- Mailchimp Integration -->
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.mailchimp.com/" target="_blank">Mailchimp autoresponder</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Mailchimp Integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="mailchimp_integration_enabled" value="0" /><input type="checkbox" value="1" name="mailchimp_integration_enabled" <?php if ($mwx_settings['mailchimp_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.mailchimp.com/" target="_blank">Mailchimp autoresponder</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Mailchimp mailing list ID number:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="mailchimp_mail_list_id_number" value="<?php echo $mwx_settings['mailchimp_mail_list_id_number']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your list ID number.<br />To get mailing list ID number - go to <a href="http://admin.mailchimp.com/lists/"><b>Lists</b></a>, click Settings for your list, find list ID number at the bottom of that settings page</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Mailchimp API key:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="mailchimp_api_key" value="<?php echo $mwx_settings['mailchimp_api_key']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your MailChimp API key. Really long number. Get your <a href="http://admin.mailchimp.com/account/api/">Mailchimp API key here</a>.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Mailchimp Interest Groups:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="mailchimp_interest_groups" value="<?php echo $mwx_settings['mailchimp_interest_groups']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Optional: Comma-delimited names of interest groups, ex:<br /><i>Dogs,Horses,Photography</i>.</div></td>
      </tr>

      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_email_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Email Settings</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>

      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Use SMTP for outgoing emails?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="smtp_enabled" value="0" /><input type="checkbox" value="1" name="smtp_enabled" <?php if ($mwx_settings['smtp_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: Use SMTP for outgoing emails (sales, subscriptions and cancellation notifications to customers and to site administrator). <b>NOTE</b>: Pear libraries must installed and Mail.php and Mail/mime.php must exists for SMTP support to work. If SMTP is enabled but these libraries are not installed at your hosting space - MemberWing-X will still try deliver emails by PHP mail()<br />Disabled: PHP mail() function will be used for outgoing emails.<br />Note: Using SMTP is preferred way to deliver emails rather than using PHP mail() due to strict anti-spam rules and settings at many internet service and email service providers.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">SMTP host name:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="smtp_host" value="<?php echo $mwx_settings['smtp_host']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Example: smtp.yourdomain.com<br />Contact your internet service provider or hosting service provider for SMTP information</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">SMTP username:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="smtp_username" value="<?php echo $mwx_settings['smtp_username']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your SMTP username</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">SMTP password:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="smtp_password" value="<?php echo $mwx_settings['smtp_password']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your SMTP password</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">SMTP port:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="smtp_port" value="<?php echo $mwx_settings['smtp_port']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your SMTP port. Default is usually 25</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Use SMTP authentication?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="smtp_use_authentication" value="0" /><input type="checkbox" value="1" name="smtp_use_authentication" <?php if ($mwx_settings['smtp_use_authentication']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: SMTP authentication will be used</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_affiliate_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Integrated Affiliate Network settings</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="50%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">MWX Integrated Affiliate Network Enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="mwx_affiliate_network_enabled" value="0" /><input type="checkbox" value="1" name="mwx_affiliate_network_enabled" <?php if ($mwx_settings['mwx_affiliate_network_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables MemberWing-X Integrated Affiliate Network functionality for digital content sales and all sales made through paypal buttons created by you.<br />Note: to track affiliate sales through your own Paypal buttons - make sure HTML code for these buttons includes proper Paypal IPN code (see MemberWing-X Paypal settings->'Paypal IPN code') and they are included in your pages in non-encrypted plaintext format.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">First affiliate wins?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_first_affiliate_wins" value="0" /><input type="checkbox" value="1" name="aff_first_affiliate_wins" <?php if ($mwx_settings['aff_first_affiliate_wins']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: Very first affiliate who direct customer to your site will be eventually credited for sale.<br />Disabled: Only last affiliate who refers buying customer will be credited for sale.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Cookie lifetime in days:</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;<input type="text" name="aff_cookie_lifetime_days" value="<?php echo $mwx_settings['aff_cookie_lifetime_days']; ?>" size="10" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Number of days before affiliate cookie will be deleted and no longer considered for referral commissions.<br /><b>Note</b>: Setting number of days to <b>0</b> will make cookie to never expire.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Minimal payout threshold:</div></td>
         <td style="background-color:#CCC;"><div align="center">$<input type="text" name="aff_min_payout_threshold" value="<?php echo $mwx_settings['aff_min_payout_threshold']; ?>" size="10" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">In $. 0=instant payment. Other=balance must reach this level for affiliate payout to be processed<br />Note: if either manual approval or manual payouts are set - instant payments will not take place.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Enable promotion to zero minimal payouts?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_promotion_to_zero_min_payout" value="0" /><input type="checkbox" value="1" name="aff_promotion_to_zero_min_payout" <?php if ($mwx_settings['aff_promotion_to_zero_min_payout']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Disabled: after payout is made - affiliate will need to accumulate minimal payout threshold again to be paid.<br />Enabled: once affiliate reaches his payout threshold first time and gets paid - his personal payout threshold will automatically be set to zero (achieved min payout immunity). He won't need to accumulate threshold ever again to be paid. Payments will become instant for him (unless manual payouts or manual sale approvals are enabled).</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Approve each affiliate sale manually?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_manual_aff_sale_approval" value="0" /><input type="checkbox" value="1" name="aff_manual_aff_sale_approval" <?php if ($mwx_settings['aff_manual_aff_sale_approval']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: each sale made by each affiliate must be manually approved by webmaster. Before sale is approved - it won't count toward payout.<br />Disabled: each successful affiliate sale is automatically approved.<br />Notes:<br />- Manual or Auto - Payout still need to reach min threshold to be paid out.<br />- This settings overrides '<i>Auto-approve affiliate sale in days</i>'.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Manual payouts to affiliates?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_manual_payouts" value="0" /><input type="checkbox" value="1" name="aff_manual_payouts" <?php if ($mwx_settings['aff_manual_payouts']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: payouts done to affiliates manually by webmaster even if affiliate reached min threshold.<br />Disabled: payouts goes out to affiliates automatically when all other conditions are met.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Auto-approve affiliate sale in days:</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;<input type="text" name="aff_sale_auto_approve_in_days" value="<?php echo $mwx_settings['aff_sale_auto_approve_in_days']; ?>" size="10" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Minimal number of days that must pass since transaction before affiliate sale will be auto-approved. Useful to make sure affiliates are only paid for referring loyal customers.<br />Note: If '<i>Approve each affiliate sale manually</i>' is set - this setting has no effect - sale will not get auto-approved even after number of days passed.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Affiliate payout value:<br />(percents of total sale)</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="aff_payout_percents" value="<?php echo $mwx_settings['aff_payout_percents']; ?>" size="10" />%</div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">In %. Ex: '<b>20</b>' means from $50 sale affiliate's share will be $10.<br />Do not add % sign! Percents off sale to pay for each affiliate.<br /><b>Note:</b> Minimum value is 5, maximum value is 90</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Tier 2 affiliate payout value:<br />(percents of total sale)</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="aff_payout_percents2" value="<?php echo $mwx_settings['aff_payout_percents2']; ?>" size="10" />%</div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">In %. Ex: '<b>5</b>' means from $50 sale second tier affiliate's payout will be $2.5<br />Second tier affiliate payouts will be additional to main(first) tier payouts.<br />Set it to <b>0</b> to disable second and higher tiers.<br />Please note that these <b>tiers are dynamic</b> and utilizing them will greatly improve your marketing abilities via twitter, social networks and media (unlike static tiers defined based only on signup referrals used by other affiliate networks)<br />Note: URL prefix for second tier affiliate ID: '<b>aff2</b>'</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Tier 3 (and higher) affiliate payout value:<br />(percents of total sale)</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="aff_payout_percents3" value="<?php echo $mwx_settings['aff_payout_percents3']; ?>" size="10" />%</div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">In %. Ex: '<b>5</b>' means from $50 sale third tier affiliate's payout will be $2.5<br />Note: this percentage will apply to higher tiers as well. Set total number of tiers below.<br />Affiliate payouts to second, third and higher tiers will be additional to main(first) tier payouts.<br />Set it to <b>0</b> to disable third and higher tiers.<br />Note: URL prefix for third tier affiliate ID: '<b>aff3</b>'</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Total number of tiers:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="aff_tiers_num" value="<?php echo $mwx_settings['aff_tiers_num']; ?>" size="2" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Total number of tiers enabled for affiliate network.<br /><b>1</b> - means only main (single) referring affiliate counts.<br /><b>2</b> - means main and second tier affiliate will count, etc...<br />Min value: <b>1</b>, max: <b>5</b>. </div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Auto approve new affiliates?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_auto_approve_affiliates" value="0" /><input type="checkbox" value="1" name="aff_auto_approve_affiliates" <?php if ($mwx_settings['aff_auto_approve_affiliates']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: each affiliate is auto-approved<br />Disabled: webmaster needs to manually approve every affiliate.<br />Note: if auto approve is ON and new sale is referred via link like this:<br /><?php echo get_bloginfo ('wpurl') . "?" . $mwx_settings['aff_affiliate_id_url_prefix'] . "=<b>john@smith.com</b>"; ?><br /> - then <b>john@smith.com</b> will be automatically added as a blog member during sale processing.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Affiliate ID prefix for links:</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;<input type="text" name="aff_affiliate_id_url_prefix" value="<?php echo $mwx_settings['aff_affiliate_id_url_prefix']; ?>" size="10" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Used by affiliates to build links to your site, such as:<br /><?php echo get_bloginfo ('wpurl') . "?" . "<b>" . $mwx_settings['aff_affiliate_id_url_prefix'] . "</b>" . "=john@smith.com"; ?> or:<br /><?php echo get_bloginfo ('wpurl') . "?" . "<b>" . $mwx_settings['aff_affiliate_id_url_prefix'] . "</b>" . "=123"; ?><br />Notes:<br />- Affiliate ID could be found under each affiliate User profile<br />- If email address is used to build referral link - it must be verified paypal email address. Make sure to inform your affiliates about that.<br />- '<b>aff</b>' can still be used by affiliates even if you changed it here</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_affiliate_payouts_page_html ()
{
   global $wpdb;
   $mwx_settings = MWX__get_settings ();

   // Get all users
   $all_users_ids = $wpdb->get_col("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY ID ASC");

   $aff_payouts_table_rows_html = "";
   $total_payouts_due           = 0;
   $total_aff_network_fees      = 0;

   $due_affiliates_ids = array();

   foreach ($all_users_ids as $affiliate_id)
      {
      $aff_info  = maybe_unserialize(get_usermeta ($affiliate_id, 'mwx_aff_info'));
      if (!is_array($aff_info) || !count($aff_info))
         continue;

      // Calculate dues for this affiliate
      // Returns: array ('aff_id'=>123, 'aff_email'=>'', 'due_payout_amt'=>'123.45');
      $aff_payout_due = MWX__CalculateDuePayoutForAffiliate ($mwx_settings, $affiliate_id);
      $aff_payout_due = $aff_payout_due['due_payout_amt'];

      // Comment this out to see stats of all affiliates.
      if (!isset($_GET['show_all_affiliates']))
         {
         if ($aff_payout_due <= 0)
            continue;
         }

      // For display/information only. Actual calculation is done on Affiliate Network Servers (not here).
      if ($aff_payout_due > 0 && !$mwx_settings['memberwing-x-license-valid'])   // MWX Aff Network fee is 0 for Premium license owners
         $aff_network_fee = max (round ($aff_payout_due / 10, 2), 0.20);
      else
         $aff_network_fee = 0;
      $total_aff_network_fees += $aff_network_fee;

      if ($aff_payout_due > 0)
         $total_payouts_due += $aff_payout_due;

      // Normalize '$aff_payout_due': .1 -> 0.10, 43.1299999 -> 43.12
      if ($aff_payout_due > 0)
         $aff_payout_due_html = '<span style="color:green;font-weight:bold;">$ ' . $aff_payout_due . '</span>';
      else if ($aff_payout_due < 0)
         $aff_payout_due_html = '<span style="color:red;font-weight:bold;">$ ' . $aff_payout_due . '</span>';
      else
         $aff_payout_due_html = '<span style="color:black;font-weight:bold;">$ ' . '0.00' . '</span>';

      $aff_network_fee_html = '<span style="color:black;font-weight:bold;">$ ' . $aff_network_fee . '</span>';

      $aff_user_edit_page_url       = rtrim(get_bloginfo ('wpurl'), '/') . '/wp-admin/user-edit.php?user_id=' . $affiliate_id;
      $user_data = get_userdata ($affiliate_id);
      $aff_name_html                = $user_data->user_login . " (" . $user_data->user_email . ")";
      $aff_name_html                = '<a href="' . $aff_user_edit_page_url . '" target="_blank">' . $aff_name_html . '</a>';

      $aff_account_status_html      = $aff_info['aff_status'];
      $aff_immune_html              = $aff_info['immune_to_min_payout_limit']?"Yes":"No";
      $aff_payout_percents_html     = max ($aff_info['payout_percents'], $mwx_settings['aff_payout_percents']) . '%';
      $aff_payout_adjustment_html   = '$ ' . $aff_info['payout_adjustment'];
//    $aff_payout_due_html          =
      if ($aff_payout_due > 0)
         {
         $js_alert_msg = "You will be redirected to Paypal to make payment to this affiliate: {$user_data->user_login} ({$user_data->user_email}). Please note: It might take a few minutes until payment to this affiliate will be reflected in his account.";
         $aff_pay_button_html = '<input type="submit" name="pay_affiliate[' . $affiliate_id . ']" value="Pay" onClick="return confirm(\'' . $js_alert_msg . '\');" />';
         $due_affiliates_ids[] = $affiliate_id;
         }
      else
         $aff_pay_button_html          = ' ----- ';


      $aff_payouts_table_rows_html .=<<<TTT
         <tr>
            <td style="background-color:white;"><div style="padding:2px;" align="left">$aff_name_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;" align="center">$aff_account_status_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;" align="center">$aff_immune_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;" align="center">$aff_payout_percents_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;" align="center">$aff_payout_adjustment_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;padding-left:10px;" align="left">$aff_payout_due_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;" align="center">$aff_network_fee_html</div></td>
            <td style="background-color:white;"><div style="padding:2px;" align="center">$aff_pay_button_html</div></td>
         </tr>
TTT;
      }

   if (!$aff_payouts_table_rows_html)
      {
      $aff_payouts_table_rows_html = '<tr><td style="background-color:#FEE;" colspan="8"><div align="center" style="padding:10px;">No affiliates are due to be paid yet according to their performance or your affiliate network settings.</div></td></tr>';
      }

   // Flatten list of affiliate ID's that are due for a payout.
   $due_affiliates_ids = implode (',', $due_affiliates_ids);

   $total_payouts_due_html       = '$ ' . $total_payouts_due;
   $total_aff_network_fees_html  = '$ ' . $total_aff_network_fees;

   $warning_row_html = '';

   $manual_payouts_script_url =
      get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/mwx-manual-payouts.php", __FILE__) . '?' . MWX__GenerateURLSecurityParam () . '&' . MWX__URL_DebugStr ($mwx_settings);

?>
<div style="margin-top:10px;">
   <form action="<?php echo $_SERVER['REQUEST_URI'] . '&show_all_affiliates=1'; ?>" method="post">
      <input type="submit" name="saa" value="Show all affiliates" />
   </form>
   <form action="<?php echo $manual_payouts_script_url; ?>" method="post">
      <table style="background-color:#555;margin-right:20px;" width="100%" border="1">
         <tr>
            <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;" colspan="8"><div align="center">Affiliates due for payouts</div></td>
         </tr>
         <tr>
            <td style="background-color:#B5FFA8;" width="25%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Affiliate</strong></div></td>
            <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Affiliate account status</strong></div></td>
            <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Immune to minimal payout limit?</strong></div></td>
            <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout % - off total sale</strong></div></td>
            <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout adjustment</strong></div></td>
            <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Payout Due</strong></div></td>
            <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Affiliate Network Fee</strong></div></td>
            <td style="background-color:#B5FFA8;" width="15%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Pay</strong></div></td>
         </tr>
         <?php echo $aff_payouts_table_rows_html; ?>
         <tr>
            <td style="background-color:#FFD;" colspan="5"><div align="right" style="padding:10px;">Total Payouts Due:</div></td>
            <td style="background-color:#FFD;"><div align="left" style="font-weight:bold;padding-left:10px;"><?php echo $total_payouts_due_html;   ?></div></td>
            <td style="background-color:#FFD;"><div align="center" style="font-weight:bold;"><?php echo $total_aff_network_fees_html;   ?></div></td>
            <td style="background-color:#FFD;"><div align="center">---</div></td>
         </tr>
         <?php echo $warning_row_html; ?>
      </table>
      <input type="hidden" name="due_affiliates_ids" value="<?php echo $due_affiliates_ids; ?>" />
   </form>
</div>
<?php

/*
   $aff_meta = array (
      'aff_status'=>$mwx_settings['aff_auto_approve_affiliates']?'active':'pending', // active, pending, declined, banned.
      'immune_to_min_payout_limit'=>'0',
      'payout_percents'=>'0',    // 0=> use system default
      'payout_adjustment'=>'0',  // Outstanding bonus (+) or outstanding payment adjustment (-)  (product refund for already paid commission)
      'sandbox_account'=>$sandbox_account,
      'payouts'=>array( array('date'=>'', 'payout_txn_id'=>'', 'payout_amt'=>''), array(...))
      'referrals'=>array(),
         // array (  Each referral sale recorded here:
         //    'txn_date'        => $_inputs['U_txn_date'],
         //    'txn_id'          => $_inputs['txn_id'],
         //    'full_sale_amt'   => $_inputs['mc_amount3_gross'],
         //    'payout_amt'      => MWX__CalculateAffiliatePayoutForSale(...),
         //    'affiliate_tier'  => $tier+1,    // 1-main affiliate, 2...5
         //    'referral_for_id' => $user_id,
         //    'status'          => $aff_txn_status,        // 'approved', 'declined', 'refunded', 'reversed', 'pending', 'adjusted'
         //    'paid'            => $_inputs['aff_paid'],   // If Adaptive payment => was paid, else:not paid.
         //    );
      );
*/

}
//===========================================================================

//===========================================================================
function MWX__render_other_systems_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();
   $save_settings =<<<SSS
      <tr>
         <td colspan="3">
            <div align="center" style="padding:6px 0;background-color:#FFD;">
               <input type="submit" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
               <input type="submit" name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
            </div>
         </td>
      </tr>
SSS;

   // Check is infusionsoft postback script is present
   $infusionsoft_postback_script = $mwx_settings['infusionsoft_post_url'];
   $infusionsoft_postback_script = preg_replace ('|\?.*|',              '',   $infusionsoft_postback_script);  // Cut off query string part
   $infusionsoft_postback_script = preg_replace ('|.*?((/[^/]+){3})$|', "$1", $infusionsoft_postback_script);  // Get name part off current directory
   $infusionsoft_postback_script = dirname (__FILE__) . $infusionsoft_postback_script;                         // Construct full name
   $infusionsoft_postback_script = str_replace ('\\', '/', $infusionsoft_postback_script);                     // Fix windows slashes if any
   if (!file_exists($infusionsoft_postback_script))
      {
      $no_infusionsoft_postback_script_warning_message =<<<SSS
      <tr>
         <td colspan="3" style="background-color:#FF0;">
            <div align="center" style="padding:5px;"><span style="color:red;font-weight:bold;font-size:120%;">WARNING:</span> InfusionSoft Postback script extension does not exists! Integration will not work.<br />Please <a href="http://www.memberwing.com/contact" target="_blank"><b>contact us</b></a> for details on how to obtain this script.</div></td>
      </tr>
SSS;
      }
   else
      $no_infusionsoft_postback_script_warning_message = '';
?>

<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Integration with other systems, services and software</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

   <!-- iDevAffiliate Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.memberwing.com/get/idev" target="_blank"><b>iDevAffiliate</b></a> affiliate tracking software</div></td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">iDevAffiliate integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="idevaffiliate_integration_enabled" value="0" /><input type="checkbox" value="1" name="idevaffiliate_integration_enabled" <?php if ($mwx_settings['idevaffiliate_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.memberwing.com/get/idev" target="_blank">iDevAffiliate</a> affiliate tracking software system</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">iDevAffiliate installation location/URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="idevaffiliate_install_dirname" value="<?php echo $mwx_settings['idevaffiliate_install_dirname']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL Location of iDevAffiliate script. Format:<br /><i>http://www.YOUR-SITE-NAME.com/idevaffiliate</i></div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>

   <!-- InfusionSoft Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.memberwing.com/get/infusionsoft/" target="_blank"><b>InfusionSoft</b></a> automated marketing system using Postback</div><div align="center" style="padding:0 6px;font-weight:bold;">Infusionsoft is software for small businesses that combines CRM, email marketing with automatic follow-up engine, and ecommerce all into one powerful system</div></td>
      </tr>
      <?php echo $no_infusionsoft_postback_script_warning_message; ?>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
<?php
$infusionsoft_instructions =<<<INFS
- login to your InfusionSoft admin panel<br />
- Click on product or web form<br />
- Click tab "Actions" or "Setup Actions"<br />
- In dropdown choose "Send an http post to another server"<br />
- In Post URL - add url of this script and <b>edit the name of your product</b> (or form) in the query string. Example:<br />
  {$mwx_settings['infusionsoft_post_url']}<br />
  (replace <b>My+Gold+Membership</b> with the name of your product or form. Use '+' instead of spaces)<br />
- Click [Save] button<br />
- When customer buys product or fills in the form:<br />
  -  notification will be sent to this script<br />
  -  new user account will be created<br />
  -  email will be dispatched to new customer as well as to website administrator with new customer information and login credentials<br />
  -  Tip: you may edit the contents of email sent to customer in MemberWingX-&gt;General Settings-&gt;Welcome email
INFS;
?>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">InfusionSoft Postback integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="infusionsoft_postback_integration_enabled" value="0" /><input type="checkbox" value="1" name="infusionsoft_postback_integration_enabled" <?php if ($mwx_settings['infusionsoft_postback_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.memberwing.com/get/infusionsoft/" target="_blank">InfusionSoft</a> system.<br />If disabled - postbacks to this script will be ignored.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">InfusionSoft Post URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="infusionsoft_post_url" value="<?php echo $mwx_settings['infusionsoft_post_url']; ?>" size="100" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of script to be called by InfusionSoft. Usage:<br /><?php echo $infusionsoft_instructions; ?></div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>

</form>
<?php
}
//===========================================================================

?>