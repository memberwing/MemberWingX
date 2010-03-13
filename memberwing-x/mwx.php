<?php
/*
Plugin Name: MemberWing-X
Plugin URI: http://www.memberwing.com/x
Version: 5.314
Author: Gleb Esman, http://www.memberwing.com/x
Author URI: http://www.memberwing.com/
Description: MemberWing-X Plugin allows you to build powerful multifunctional membership sites with your own integrated affiliate network. MemberWing-X allows to sell access to premium content on one-time or recurring payments basis. MWX Integrated Instant Affiliate Network allows you to offer instant signup, instant approval and instant payments to affiliates. MWX Instant Affiliate Network allows you to share all paypal payments that are coming through your site with your affiliates.
*/

define('MEMBERWING_X_VERSION',  '5.314');
define('MEMBERWING_X_EDITION',  'GA');

/*
5.314
- Out of beta!
- Added ability for users to set their memberwing affiliate link in admin panel and have "powered by... " link pointing to their affiliate link.
- Fixed glitch in API interface

5.313
- Fixed tiers number for processing. Do not process more tiers than specified in settings (notify-utils)

5.312
- Added API call to build affiliate link, fixed "use get" flag for API calls.

5.311
- MWX api key is added for integration with other systems (added in admin panel).
- mwx-api.php added exporting ability to create affiliates from third-party applications.
- Added support for multiple dynamic tiers (up to 5) for MWX affiliate network. Affiliate network is almost ready to go viral (stay tuned - it is going to be huge)!

5.301
- MW affiliate network fee is gone for premium license owners.
- License validation protocol improved. All pre 5.301 users need to upgrade.

5.2xx
- Early beta.

*/

// Note: Using 'include_once' here will prevent mwx-paypal-x.php to get loaded independently
include (dirname(__FILE__) . '/mwx-include-all.php');

//---------------------------------------------------------------------------
// Insert hooks
register_activation_hook   (__FILE__, 'MWX__activated');
add_action                 ('admin_menu',                'MWX__admin_menu',            222);
add_filter                 ('the_content',               'MWX__the_content',           222);
add_filter                 ('the_content_limit',         'MWX__the_content',           222);
add_filter                 ('the_excerpt',               'MWX__the_content',           222);
add_filter                 ('the_content_rss',           'MWX__the_content',           222);
add_filter                 ('the_excerpt_rss',           'MWX__the_content',           222);

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

add_action                 ('init', 'MWX__init');

// New user registration - integration with autoresponders.
add_action                 ('user_register',             'MWX__user_register');
function MWX__user_register ($user_id) { MWX__AddUserToAutoresponder ($user_id, MWX__get_settings ()); }

// 404 handling - no 'join' page reminder - in case webmaster forgot to create one
add_action('template_redirect', 'MWX__template_redirect');

function MWX__template_redirect()
{
if (is_404() && preg_match ('@subscribe/?$@', $_SERVER['REQUEST_URI']))
   {
   include (dirname(__FILE__) . '/subscribe-page-404.php');
   exit;
   }
}
//---------------------------------------------------------------------------


//===========================================================================
function MWX__init ()
{
   // Process user's cookie and initialize current refering affiliate ID (if present).
   MWX__SetCurrentAffiliateRawID (MWX__SetCookie ());
}
//===========================================================================

//===========================================================================
// Initial activation code here such as: DB tables creation, storing initial settings.

function MWX__activated ()
{
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

    update_option ('MemberWing-X', $mwx_default_options);
    MWX__Validate_License ($mwx_default_options['memberwing-x-license_code']);
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
      get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/mwx-admin-icon.gif", __FILE__)          // Icon URL
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWingX General Settings',           // Page Title
      'General Settings',                       // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings',                  // Handle - First submenu's handle must be equal to parent's handle to avoid duplicate menu entry.
      'MWX__render_general_settings_page'       // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Paypal Settings',           // Page Title
      'Paypal Settings',                        // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-paypal',           // Handle
      'MWX__render_paypal_settings_page'        // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Integrated Affiliate System Settings', // Page Title
      'Affiliate Network Settings',             // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-affiliate',        // Handle
      'MWX__render_affiliate_settings_page'     // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Affiliate payouts',        // Page Title
      'Affiliate Stats and Payouts',                      // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-affiliate-payouts',// Handle
      'MWX__render_affiliate_payouts_page'      // Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Autoresponders Settings',  // Page Title
      'Autoresponders',                         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-autoresponders',   // Handle
      'MWX__render_autoresponders_settings_page'// Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Email Settings',           // Page Title
      'Email Settings',                         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-email',            // Handle
      'MWX__render_email_settings_page'// Function
      );
   add_submenu_page (
      'memberwing-x-settings',                  // Parent
      'MemberWing-X: Integration with Other Systems, Services and Software',  // Page Title
      'Integration with other systems',         // Menu Title
      'administrator',                          // Capability
      'memberwing-x-settings-other-systems',   // Handle
      'MWX__render_other_systems_settings_page'// Function
      );
}

function MWX__render_general_settings_page ()         { MWX__render_settings_page   ('general'); }
function MWX__render_paypal_settings_page ()          { MWX__render_settings_page   ('paypal'); }
function MWX__render_autoresponders_settings_page ()  { MWX__render_settings_page   ('autoresponders'); }
function MWX__render_email_settings_page ()           { MWX__render_settings_page   ('email'); }
function MWX__render_affiliate_settings_page ()       { MWX__render_settings_page   ('affiliate settings'); }
function MWX__render_affiliate_payouts_page ()        { MWX__render_settings_page   ('affiliate payouts'); }
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
      MWX__reset_settings ();
echo <<<HHHH
<div align="center" style="background-color:#FFA;padding:5px;font-size:120%;border: 1px solid gray;margin:5px;">
Settings reverted to all defaults
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
   global $id;             // ID of current article/post/page
   global $current_user;   // ID of currently logged-on user
   $user_id = $current_user->id;    // All user's data: $current_user_data = get_userdata ($current_user->id);

   $mwx_settings = MWX__get_settings ();

   // Automatic Integration Code Insertion for Paypal Buttons
   if ($mwx_settings['paypal_integration_code_auto_insert'])
      $content = preg_replace_callback ('@<form[^>]+action=[\'\"]https://www\.(sandbox\.)?paypal\.com.+?</form>@si', 'MWX__preg_replace_callback', $content);

   if ($mwx_settings['mwx_affiliate_network_enabled'])
      {
      $custom_data = MWX__PackCustomData ($mwx_settings['secret_password'], MWX__GetCurrentAffiliateRawID());
      $content = preg_replace ('/MWX_AFFILIATE_TRACKING_DATA/', $custom_data, $content);
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

   if (preg_match ($reg_exp, $content, $matches, PREG_OFFSET_CAPTURE))
      {
      // Premium marker found => premium article.
      $premium_content     = TRUE;
      $marker_offset       = $matches[0][1];
      $conditions_string   = $matches[1][0]; // "4.95|gold" for input: {{{4.95|gold}}}

      if (MWX__CurrentUserCanAccessArticle ($id, $user_id, $conditions_string, $mwx_settings))
         {
         // Logged on member already purchased the article OR is a member of eligible membership product (including free membership) OR reached maturity
         $user_allowed = true;
         }
      }
   else
      $user_allowed = true;   // Not a premium content.

   if ($user_allowed)
      {
      if ($premium_content)
         $content = preg_replace ($reg_exp, "", $content); // Melt premium marker
      return $content;
      }

   // Current user is not authorized to view this article. Hide it behind premium content warning
   $warn_msg = MWX__AssemblePremiumContentWarningMessage ($id, $user_id, $conditions_string);

   // Before content - free teaser.
   $free_teaser = substr ($content, 0, $marker_offset);

   // Last part - build terminating tags that are left unterminated within teaser. This is necessary to keep HTML from breaking up.
   $trailer = MWX__BuildTrailingTags ($free_teaser);

   // Final assembly
   // Fix issues with excerpt screwing premium content warning message.
   if (is_feed())
      $content = $free_teaser . $warn_msg . $trailer;
   else
      $content = $free_teaser . $display_none_prefix . '{{{' . $conditions_string . '}}}</div>' . $warn_msg . $trailer;

   return $content;
}

// Assists in automatic integration code insertion for Paypal Buttons
function MWX__preg_replace_callback ($match)
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
//===========================================================================

//===========================================================================
function MWX__BuildTrailingTags ($teaser)
{
   // Melt beginning text
   $teaser = preg_replace ('@^[^<]+@', '', $teaser);

   // Melt ending text
   $teaser = preg_replace ('@[^>]+$@', '', $teaser);

   // Kill everything in between tags
   $teaser = preg_replace ('@>[^<]+@', '>', $teaser);

   // Kill all self terminating tags
   $teaser = preg_replace ('@<[^>]*/>@', '', $teaser);

   // Melt inner attributes of tags.
   $teaser = preg_replace ('@<([a-zA-Z0-9]+)[^>]+>@', "<$1>", $teaser);

   // Get rid of unterminated <br> and <hr> tags.
   $teaser = preg_replace ('@<[bBhH][rR]>@', '', $teaser);

   // Repeat killing immediate tag pairs <h3></h3> until none exist.
   do
      {
      $teaser = preg_replace ('@<([a-zA-Z0-9]+)></\1>@', '', $teaser, -1, $count);
      }
   while ($count);


   // Final processing. Reverse tags order and add termination </ to each of them.
   $teaser = str_replace('<', '</', implode(array_reverse(explode ('[@]', str_replace ('><', '>[@]<', $teaser)))));

   return $teaser;
}
//===========================================================================

//===========================================================================
//
// $conditions_string - what's inside of {{{...}}} brackets.
// Returns: TRUE-can access, FALSE-cannot
function MWX__CurrentUserCanAccessArticle ($article_id, $user_id, $conditions_string, $mwx_settings)
{
///!!! Check for legacy mode and match current_user_can(...) with conditions_string's "gold", "silver", etc...

///!!! INSERT MATURITY TEST HERE AS WELL. Could be:
// gold&maturity=5   // must own "gold" product *AND* be at least 5 days mature
// maturity=5        // could just be registered member at least 5 days mature

   if ($user_id < 1 || (current_user_can ('edit_users') && $mwx_settings['admin_acts_like_regular_visitor']))
      return FALSE;  // Non logged on visitor (or admin acting like visitor) cannot see any premium post/page at any time.

   $conditions = explode ('|', $conditions_string);

   // When user is logged on AND article allows access to ANY blog member - then 'true'. Case: {{{*}}} or {{{0}}}
   if (in_array('0', $conditions) || in_array('*', $conditions))
      return TRUE;

   if (current_user_can ('edit_users'))
      return TRUE;

   // Samples of premium markers:
   // ===========================
   //    No product names can begin with '0', '*'
   //    {{{*}}} or {{{0.00}}} or {{{0}}} -  just become a free member of blog to see this article
   //    {{{4.95}}}                 -  buy access to this article for 4.95
   //    {{{gold}}}                 -  must be a subscriber to "gold membership" to access this article
   //    {{{4.95|gold}}}            -  can either buy this article for 4.95 OR be a subscriber to "gold membership" to access it.
   //    {{{2.95|silver|gold}}}     -  can either buy this article for 2.95 OR be a subscriber to either "silver" or "gold" memberships to access it.
   //    {{{2.95|membership}}}      -  can either buy this article for 2.95 OR be a subscriber to any "membership" such as: "silver membership" or "my super membership plan" to access it. Case insensitive substring search within membership product name.
   //    {{{4.95|gold}}}            -  can either buy this article for 2.95 OR be a subscriber to "gold" to see this article
   //    {{{beginner}}}             -  must be a subscriber to "beginner student" to see this article.
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
   $products_purchased = maybe_unserialize(get_usermeta ($user_id, 'mwx_purchases'));

   if (is_array($products_purchased))
      {
      foreach ($products_purchased as $product)
         {
         if ($product['product_status'] != 'active')
            continue;   // Skip inactive products.

         // Product ID matches current page OR product name matches required subscription/conditions(maturity).
         if ($article_id == $product['product_id'])
            return TRUE;

         // Try to match name of one of purchased subscriptions to the name of one of required subscriptions.
         foreach ($conditions as $condition_short_string)
            {
            $condition_short_string = trim ($condition_short_string, ' $');
            if (!$condition_short_string) continue;

            //  stristr ($haystack, $needle)
            if (!is_numeric($condition_short_string) && stristr ($product['product_name'], $condition_short_string))
               return TRUE;
            }
         }
      }

   //---------------------------------------
   // Check for legacy MemberWing conditions
   if ($mwx_settings['memberwing_legacy_compatibility_mode'])
      {
      foreach ($conditions as $condition_short_string)
         {
         if ($condition_short_string == 'bronze' || $condition_short_string == 'silver' || $condition_short_string == 'gold' || $condition_short_string == 'platinum')
            {
            if (current_user_can ('read_'.$condition_short_string))
               return TRUE;
            }
         }
      }
   //---------------------------------------

   return FALSE;
}
//===========================================================================

//===========================================================================
function MWX__AssemblePremiumContentWarningMessage ($article_id, $user_id, $conditions_string)
{

   // Variables to initialize
   $current_page           = ltrim ($_SERVER['REQUEST_URI'], '/');
   $login_msg_required     = ($user_id > 0)?false:true;
      $separator1 = "";
   $subscribe_msg_premium_required = false;
   $subscribe_msg_free_required    = false;
      $separator2 = "";
   $buy_msg_required               = false;
   $item_price = 0;

   $mwx_settings = MWX__get_settings ();

   // Initialize '$subscribe_msg_premium_required' and '$buy_msg_required'.
   $conditions_arr = explode ('|', $conditions_string);
   foreach ($conditions_arr as $condition)
      {
      $condition = trim($condition, ' $');   // " $4.95" -> "4.95"

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
      $paypal_buttons = MWX__CreatePaypalButtons ($article_id, get_the_title ($article_id), $item_price);
      $premium_content_warning_message = preg_replace ('|\{BUYCODE\}|', $paypal_buttons['buy_now_button_code'], $premium_content_warning_message);
      }

   if (!$mwx_settings['memberwing-x-license-valid'] || $mwx_settings['show-powered-by'])
      $premium_content_warning_message .= $mwx_settings['brd'];

   return ($premium_content_warning_message);
}
//===========================================================================

//===========================================================================
//
// Build HTML code for paypal buttons. Returns: array ('buy_now_button_code'=>'...', 'add_to_cart_button_code'=>'...', 'view_cart_button_code'=>'...')
function MWX__CreatePaypalButtons ($article_id, $article_name, $price)
{
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

   $item_name = "Item: $article_name (id:$article_id)";
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
<input type="image" src="{$mwx_settings['buy_now_button_image']}" border="0" name="submit" alt="Buy '$item_name'. Instant access!" title="Powered by Paypal" style="vertical-align:middle;border:none !important;width:inherit;">
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
<input type="image" src="{$mwx_settings['add_to_cart_button_image']}" border="0" name="submit" alt="Buy '$item_name'. Instant access!" title="Powered by Paypal" style="vertical-align:middle;border:none !important;width:inherit;">
</form>
TTT;

   $paypal_buttons['view_cart_button_code'] =<<<TTT
<form target="paypal" action="$payment_script_url" method="post" style="display:inline;">
<input type="hidden" name="cmd" value="_cart">
<input type="hidden" name="business" value="$business_email">
<input type="hidden" name="display" value="1">
<input type="hidden" name="custom" value="$custom_data">
<input type="hidden" name="notify_url" value="$paypal_ipn_url">
<input type="image" src="{$mwx_settings['view_cart_button_image']}" border="0" name="submit" alt="View shopping cart" title="Powered by Paypal" style="vertical-align:middle;border:none !important;width:inherit;">
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
   if (preg_match ('#(slurp|bot|sp[iy]der|scrub(by|the)|crawl(er|ing|@)|yandex)#i', $_SERVER['HTTP_USER_AGENT']))
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

      setcookie("memberwing-x", MWX__EncodeCookie ($mwx_cookie), strtotime("+5 years"), defined('SITECOOKIEPATH')?SITECOOKIEPATH:"/");
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
   get_currentuserinfo();

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
   get_currentuserinfo();

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
   get_currentuserinfo();

   if (!$user_ID)
      {
      //no user logged in - melt comments
      // Melt everything outside of tags, leaving all tags in tact. Tags == <...>
      $content = preg_replace ('#(?<=^|\>)[^\<]*#', '', $content);
      // Melt all inner attributes of tags
      $content = preg_replace ('/<([a-zA-Z0-9]+)\s[^>]*>/', "<$1>", $content1);
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

?>