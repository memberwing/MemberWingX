<?php

global $g_MWX__default_welcome_email;
$g_MWX__default_welcome_email =
   '{FIRST_NAME} {LAST_NAME}<br />Your purchase of "{ITEM_NAME}" is confirmed.' .
   '<br />Your username / password are: {USERNAME} / {PASSWORD}' .
   '<br />Click here to Login: {BLOG_LOGIN_URL}' .
   '<br />After you login you will be able to see content that you\'ve purchased access to.' .
   '<br /><br />Please contact us for any questions! Sincerely,<br/>{BLOG_ROOT_URL}';

// '{CURRENT_PAGE}' is current page URL without domain name and front slash.
global $g_MWX__default_premium_content_warning;
$g_MWX__default_premium_content_warning = '<div style="clear:both;background-color:#FFC;padding:3px;border:2px solid #E40000;margin:0 0 5px;font-size:0.96em;line-height:1.21em;">{PROMO_MSG} {ACCESS_DELAYED_MSG}<div>{LOGIN_MSG} {SUBSCRIBE_MSG} {BUY_MSG}</div></div>';

global $g_MWX__plugin_directory_url;
$g_MWX__plugin_directory_url = get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1", __FILE__);

global $g_MWX__paypal_ipn_url;
$g_MWX__paypal_ipn_url = $g_MWX__plugin_directory_url . '/mwx-notify-paypal.php';

global $g_MWX__pay4me_form_submit_url;
$g_MWX__pay4me_form_submit_url = $g_MWX__plugin_directory_url . '/mwx-pay4me-form.php';

//-------------------------------------------------------------
// Digital Online Store default HTML templates
// --- t1 template --- //
global $T_main_container_t1;
$T_main_container_t1 =<<<TTT
         <div id="mwx-dos-products-wrapper-div">
            <table id="mwx-dos-products-1" summary="Digital Products">
               <thead>
                  <tr>
                     <th class="rounded-top-left" scope="col"></th>
                     <th scope="col"><div align="center">Digital Products</div></th>
                     <th class="rounded-top-right" scope="col"></th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <td class="rounded-bottom-left"></td>
                     <td><div align="center">{DOS_MWX_FOOTER}</div></td>
                     <td class="rounded-bottom-right"></td>
                  </tr>
               </tfoot>
               <tbody>
               {ONLINE_STORE_ITEMS_HTML}
               </tbody>
            </table>
         </div>
TTT;

global $T_digital_item_directory_t1;
$T_digital_item_directory_t1 =<<<TTT
            <tr>
               <td colspan="3">
                  <div align="center" class="dir-info-wrapper">
                     <b>{DIR_PRODS_NAME_TXT}</b><br />{DIR_PRODS_DESCRIPTION}
                  </div>
               </td>
            </tr>
TTT;

global $T_digital_item_allowed_t1;
$T_digital_item_allowed_t1 =<<<TTT
            <tr>
               <td colspan="3">
                  <div class="product-wrapper" description="{PROD_DESCRIPTION}">
                     <div class="product-images">
                        <div class="product-image-1"><img src="{ICON_URL_1}" srcbig="{BIGIMG_URL_1}" /></div>
                     </div>
                     <div class="product-name" align="center">{PROD_NAME_TXT}</div>
                     <div class="buy-download-buttons">
                        <div class="download-link-wrapper">
                           <a target="_blank" href="{SAFE_DOWNLOAD_URL}">{DIR_NAME}{FILE_NAME}&nbsp;&nbsp;&nbsp;<img src="{URL_DOWNLOAD_BUTTON}" /></a>
                        </div>
                     </div>
                     <div class="product-description">
                        <div class="product-images"><div class="product-images-2x">{ICON_IMGS_2X}</div></div>
{IF_GOOGLE}
                        {PROD_DESCRIPTION}
{ELSE}
                        {PROD_DESCRIPTION_EXCERPT}
{ENDIF}
                     </div>
                  </div>
               </td>
            </tr>
TTT;

global $T_digital_item_denied_t1;
$T_digital_item_denied_t1 =<<<TTT
            <tr>
               <td colspan="3">
                  <div class="product-wrapper" description="{PROD_DESCRIPTION}">
                     <div class="product-images">
                        <img src="{ICON_URL_1}" srcbig="{BIGIMG_URL_1}" />
                     </div>
                     <div class="product-name" align="center">{PROD_NAME_TXT}{IF_FILE_WITH_PREVIEW} [<a target="_blank" href="{SAFE_DOWNLOAD_URL}?r={RANDOM_NUMBER}">Preview</a>]{ELSE}{ENDIF}</div>
                     <div class="buy-download-buttons">
{IF_CUSTOM_BUY_CODE}
   {IF_PROD_PRICE}
                        <div class="price-tag-wrapper">{PROD_PRICE_TXT}</div>
   {ELSE}{ENDIF}
                        <div class="buy-button-wrapper">{CUSTOM_BUY_CODE}</div>
{ELSE}
   {IF_PROD_PRICE}
                        <div class="price-tag-wrapper">{PROD_PRICE_TXT}</div>
                        <div class="buy-button-wrapper">{BUY_NOW_BUTTON}</div>
   {ELSE}{ENDIF}
{ENDIF}
                     </div>
                     <div class="product-description">
                        <div class="product-images"><div class="product-images-2x">{ICON_IMGS_2X}</div></div>
{IF_GOOGLE}
                        {PROD_DESCRIPTION}
{ELSE}
                        {PROD_DESCRIPTION_EXCERPT}
{ENDIF}
                     </div>
                  </div>
               </td>
            </tr>
TTT;

// --- t2 template --- //

global $T_main_container_t2;
$T_main_container_t2 =<<<TTT
<div style="">
   {ONLINE_STORE_ITEMS_HTML}
</div>
TTT;

global $T_digital_item_directory_t2;
$T_digital_item_directory_t2 =<<<TTT
<div align="center" style="border: 5px solid rgb(204, 204, 204);background-color:#FFE;margin:4px;padding:5px;">
   <div style="font:100% Verdana,Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;">
      {DIR_PRODS_NAME_TXT}
   </div>
   <div style="font:100% Verdana,Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;">
      {DIR_PRODS_DESCRIPTION}
   </div>
</div>
TTT;

global $T_digital_item_allowed_t2;
$T_digital_item_allowed_t2 =<<<TTT
            <div style="border: 5px solid rgb(204, 204, 204);margin:4px;">
               <table class="mwx-dos-product-t2" description="{PROD_DESCRIPTION}" cellspacing="0" cellpadding="0" border="0" style="width:100%;text-align:center;vertical-align:middle;">
                  <tbody>
                     <tr style="vertical-align: middle;">
                        <td class="product-image-td-t2" width="64" style="text-align: left; vertical-align: top;" rowspan="2">
                           <div style="padding:6px;height:64px;overflow:hidden;border-right:1px solid rgb(204, 204, 204);">
                              <img src="{ICON_URL_1}" srcbig="{BIGIMG_URL_1}" style="width:64px;" />
                           </div>
                        </td>
                        <td class="product-name-td-t2" style="text-align:left;" colspan="2">
                           <div class="product-name-t2" style="height:11px;overflow:hidden;color:010E8F;padding:6px 4px 2px 4px;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;">
                           {PROD_NAME_TXT}
                           </div>
                        </td>
                     </tr>
                     <tr style="vertical-align: middle;">
                        <td class="product-description-td-t2" style="text-align:left;" colspan="2">
                           <div class="product-description-t2" style="border-top:1px solid rgb(204, 204, 204);border-bottom:1px solid rgb(204, 204, 204);padding:0 4px 0 4px;color:#4B4B4B;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:10px;font-weight:normal;">
{IF_GOOGLE}
                              {PROD_DESCRIPTION}
{ELSE}
                              {PROD_DESCRIPTION_EXCERPT}
{ENDIF}
                           </div>
                        </td>
                     </tr>
                     <tr style="vertical-align: middle;">
                        <td style="" colspan="3">
                           <div style="text-align:left;padding-left:6px;">
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div align="right" style="padding-right:8px;color:0E0E0E;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;">
                           {PROD_PRICE_TXT}
                           </div>
                        </td>
                        <td colspan="2" style="padding-bottom:4px;">
                           <div style="padding-left:10px;">
                              <div style="float:left;color:#4B4B4B;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:10px;font-weight:normal;line-height:17px;">&nbsp;-or-&nbsp;</div>
                              <div style="float:left;">
                                 <div><a target="_blank" href="{SAFE_DOWNLOAD_URL}?r={RANDOM_NUMBER}"><img style="vertical-align:middle;" src="$g_MWX__plugin_directory_url/images/download_amzn.gif" /></a></div>
                              </div>
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
TTT;

global $T_digital_item_denied_t2;
$T_digital_item_denied_t2 =<<<TTT
            <div style="border: 5px solid rgb(204, 204, 204);margin:4px;">
               <table class="mwx-dos-product-t2" description="{PROD_DESCRIPTION}" cellspacing="0" cellpadding="0" border="0" style="width:100%;text-align:center;vertical-align:middle;">
                  <tbody>
                     <tr style="vertical-align: middle;">
                        <td class="product-image-td-t2" width="64" style="text-align: left; vertical-align: top;" rowspan="2">
                           <div style="padding:6px;height:64px;overflow:hidden;border-right:1px solid rgb(204, 204, 204);">
                              <img src="{ICON_URL_1}" srcbig="{BIGIMG_URL_1}" style="width:64px;" />
                           </div>
                        </td>
                        <td class="product-name-td-t2" style="text-align:left;" colspan="2">
                           <div class="product-name-t2" style="height:11px;overflow:hidden;color:010E8F;padding:6px 4px 2px 4px;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;">
                           {PROD_NAME_TXT}
                           </div>
                        </td>
                     </tr>
                     <tr style="vertical-align: middle;">
                        <td class="product-description-td-t2" style="text-align:left;" colspan="2">
                           <div class="product-description-t2" style="border-top:1px solid rgb(204, 204, 204);border-bottom:1px solid rgb(204, 204, 204);padding:0 4px 0 4px;color:#4B4B4B;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:10px;font-weight:normal;">
{IF_GOOGLE}
                              {PROD_DESCRIPTION}
{ELSE}
                              {PROD_DESCRIPTION_EXCERPT}
{ENDIF}
                           </div>
                        </td>
                     </tr>
                     <tr style="vertical-align: middle;">
                        <td style="" colspan="3">
                           <div style="text-align:left;padding-left:6px;">
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div align="right" style="padding-right:8px;color:#0E8F0E;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;">
                           {PROD_PRICE_TXT}
                           </div>
                        </td>
                        <td colspan="2" style="padding-bottom:4px;">
                           <div style="padding-left:10px;">
                              <div style="float:left;">
                                 {BUY_NOW_BUTTON}
                              </div>
{IF_FILE_WITH_PREVIEW}
   {IF_PROD_PRICE}
                              <div style="float:left;color:#4B4B4B;font:100% Verdana,Arial,Helvetica,sans-serif;font-size:10px;font-weight:normal;line-height:inherit;vertical-align:middle;">&nbsp;-or-&nbsp;</div>
   {ELSE}{ENDIF}
                              <div style="float:left;">
                                 <div><a target="_blank" href="{SAFE_DOWNLOAD_URL}?r={RANDOM_NUMBER}"><img style="vertical-align:middle;" src="$g_MWX__plugin_directory_url/images/preview_amzn.gif" /></a></div>
                              </div>
{ELSE}{ENDIF}
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
TTT;
//-------------------------------------------------------------

//-------------------------------------------------------------
global $_mwx_form_template;
$_mwx_form_template =<<<TTT
	<div style="margin:10px;border:1px solid #DDDDDD;">
		<div align="center" style="background-color:#EEEEEE;margin:10px;font-size:140%;">Gold Membership:<span style="margin:0 10px 0 50px;">&#8358;</span><b style="color:#008800;">100</b></div>
		<form style="margin:10px;" method="post" action="{$g_MWX__pay4me_form_submit_url}"><div style="display:none;">
			<input type="hidden" name="mwxform__item_name"   	value="Gold Membership" />
			<input type="hidden" name="mwxform__item_description" 	value="Full Access Gold Membership" />
			<input type="hidden" name="mwxform__item_price"  	value="100" />
			<input type="hidden" name="merchant_service_id" 	value="{{{merchant_service_id}}}" />
		  </div><table width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr>
					<td>Your name:</td>
					<td><input type="text" name="mwxform__payer_name" /></td>
				</tr>
				<tr>
					<td>Your email:</td>
					<td><input type="text" name="mwxform__payer_email" /></td>
				</tr>
				<tr>
					<td>Desired username:</td>
					<td><input type="text" name="mwxform__payer_username" /></td>
				</tr>
				<tr>
					<td>Desired password:</td>
					<td><input type="password" name="mwxform__payer_password" /></td>
				</tr>
				<tr>
					<td colspan="2"><button style="font-size:120%;" type="submit" name="mwxform__buybutton">Sign-up</button></td>
				</tr>
			</table>
		</form>
	</div>
TTT;
//-------------------------------------------------------------

global $g_MWX__config_defaults;
$g_MWX__config_defaults = array (
// ------- General Settings
   'memberwing-x-license_code'            => '',
   'your-memberwing-affiliate-link'       => '',
   'show-powered-by'                      => '1',
   'default_primary_payment_processor'    => 'paypal',
   'payment_success_page'                 => get_bloginfo ('wpurl'),
   'payment_cancel_page'                  => get_bloginfo ('wpurl'),
   'buy_now_button_image'                 => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/buy_now_amzn.gif",     __FILE__),
   'add_to_cart_button_image'             => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/add_to_cart_amzn.gif", __FILE__),
   'view_cart_button_image'               => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/view_cart_amzn.gif",   __FILE__),
   'download_button_image'                => get_bloginfo ('wpurl') . preg_replace ('#^.*[/\\\\](.*?)[/\\\\].*?$#', "/wp-content/plugins/$1/images/download_amzn.gif",   __FILE__),
   'premium_content_warning'              => MWX__base64_encode ($g_MWX__default_premium_content_warning),
   'login_msg'                            => '<a target="_blank" href="' . rtrim(get_bloginfo ('wpurl'), '/') . '/wp-login.php?redirect_to=/{CURRENT_PAGE}">Login</a>',
   'subscribe_url_premium'                => rtrim(get_bloginfo ('wpurl'), '/') . '/subscribe/',
   'subscribe_msg_premium'                => '<a target="_blank" href="{SUBSCRIBE_URL_PREMIUM}">Subscribe</a>',
   'subscribe_url_free'                   => rtrim(get_bloginfo ('wpurl'), '/') . '/wp-login.php?action=register',
   'subscribe_msg_free'                   => '<a target="_blank" href="{SUBSCRIBE_URL_FREE}">Register</a>',
   'promo_msg_premium'                    => 'The rest of this article is available for premium members only.',
   'promo_msg_free'                       => 'The rest of this article is available for members. Membership is Free!',
   'access_delayed_msg'                   => '<br />You will be allowed to read this article <span style="border-bottom:1px dotted #E40000;">in <span style="font-weight:bold;color:#E40000;">{TIME_LEFT}</span></span>. For immediate or faster access:<br />',
   'buy_msg'                              => 'Buy this article: <span style="font-weight:bold;color:#080;">${PRICE}  </span> {BUYCODE}',
   'welcome_email_from_name'                => 'Webmaster',
   'welcome_email_subject'                => 'Thank you for your purchase',
   'welcome_email_body'                   => MWX__base64_encode ($g_MWX__default_welcome_email),
   'keep_access_for_ended_subscriptions'  => '1',  // Keep access active if subscription ended normally. If "0"-access will auto-stop
   'delete_emptyhanded_user'              => "0",
   'keep_cancelled_subs_active_till_eot'  => "1",
   'hide_comments_from_non_logged_on_users' => '1',
   'memberwing_legacy_compatibility_mode' => "0",  // Makes MemberWing-X compatible with old-style premium markers: {+++}
   'admin_acts_like_regular_visitor'      => '1',  // Enabled: admin will not see premium content if it is protected. Disabled: Admin sees all. Useful for testing.
   'mwx_api_endpoint'                     => $g_MWX__plugin_directory_url . '/mwx-api.php',  // URL of API endpoint
   'mwx_api_key'                          => substr(md5(microtime()), 0, 16), // Also used to protect custom data and prevent spoofing.
   'show_premium_content_warning_for_home_page'      => "1",
   'show_premium_content_warning_for_category_pages' => "0",
   'first_click_free_enabled'             => "0",
   'first_click_free_clicks_allowed'      => "1",  // 1 (default) ... 10
   'ip_protection_enabled'								=> false,
   'ip_protection_max_allowed_addresses'	=> 3,		// office, home, cell
   'ip_protection_cidr_mask'							=> 24,	// N.N.N.*
   'ip_protection_access_denied_message'	=> '<span style="font-weight:bold;color:red;background-color:#FFFF88;"> Warning! Your account has been accessed from too many different locations.<br />Access is denied. Please contact site administrator for assistance. </span>',
   'ip_protection_log_history_size'				=> 25,		// number of IP log entries to keep for each user

// ------- DCP - Digital Content Protection + TraceFusion
   'protected_files_physical_addr'        => rtrim(str_replace ('\\', '/', ABSPATH), '/') . '/PREMIUM_FILES',   // Non trail-slashed name of directory
   'protected_files_logical_addr'         => 'premium',            // off blog's root
   'locked_access_directory_names'        => "UPLOADS,temporary",
   'unrestricted_access_directory_names'  => "js,css,img",  // Names of dirs reserved for unrestricted access (images, icons, styles, helper javascripts, etc...)
   'individual_access_directory_names'    => "downloads,files",   // Names of dirs containing files for single purchase. These dirnames are not matched to product names
   'group_access_directory_names'         => "membership,subscription,bronze,silver,gold,platinum",   // Names of dirs reserved for matching against product names to give access to files under it. ./gold/file.pdf - user must owns "...gold.." product
   'tracefusion_tracing_enabled'          => '0',

// ------- Digital Online Store Builder
   'dos_primary_payment_processor'        => 'paypal',
   'dos_current_active_template'          => 't1',          // Last edited template in admin screen. Allows admin screen persistance
   'dos_templates' => array (
      't1'=>      // Template name. t1 = default MWX dos template
         // Each template is stored as assoc. array.
         array (
            'dos_style_stylesheet_file'            => $g_MWX__plugin_directory_url . '/css/dos-builder/t1/mwx-dos-builder.css',
            'dos_style_javascript_file'            => $g_MWX__plugin_directory_url . '/js/dos-builder/t1/mwx-dos-builder.js',
            'dos_style_main_container'             => $T_main_container_t1,
            'dos_style_directory_template'         => $T_digital_item_directory_t1,
            'dos_style_item_allowed_template'      => $T_digital_item_allowed_t1,
            'dos_style_item_denied_template'       => $T_digital_item_denied_t1,
            ),
      't2'=>      // Template name. t1 = default MWX dos template
         // Each template is stored as assoc. array.
         array (
            'dos_style_stylesheet_file'            => $g_MWX__plugin_directory_url . '/css/dos-builder/t2/mwx-dos-builder.css',
            'dos_style_javascript_file'            => $g_MWX__plugin_directory_url . '/js/dos-builder/t2/mwx-dos-builder.js',
            'dos_style_main_container'             => $T_main_container_t2,
            'dos_style_directory_template'         => $T_digital_item_directory_t2,
            'dos_style_item_allowed_template'      => $T_digital_item_allowed_t2,
            'dos_style_item_denied_template'       => $T_digital_item_denied_t2,
            ),
      ),
   'dos_mwx_footer_html'                  => 'Powered by <a href="http://www.memberwing.com/"><b>MemberWing-<span style="color:red;">X</span></b></a> <a href="http://www.memberwing.com/">Digital Online Store Builder</a>',
   'dos_api_endpoint'                     => $g_MWX__plugin_directory_url . '/dos-widget/mwx-dos-api.php',

// ------- Categories Settings
   'categories_settings'                  => "",   // Will be dynamically set

// ------- Products Settings
   'products_lifetimes'                   => "threedays:3d\nquarternews:90d",   // keyword:NNN, where NNN = lifetime of product in days
   'products_access_delays'               => "MarketObserver:7d\nDayTrader:24h\nGoldInvestor:20m",

// ------- Invitation codes
   'invitation_codes'                     => array(),

// ------- Paypal Settings
   'paypal_email'                         => get_option('admin_email')?get_option('admin_email'):'',
   'paypal_currency_code'                 => 'USD',
   'paypal_ipn_url'                       => $g_MWX__paypal_ipn_url,
   'paypal_integration_code_html'         => '<input type="hidden" name="notify_url" value="' . $g_MWX__paypal_ipn_url . '">' . "\n" .
                                             '<input type="hidden" name="custom" value="MWX_AFFILIATE_TRACKING_DATA">',
   'paypal_integration_code_auto_insert'  => '1',
   'paypal_sandbox_enabled'               => '',
   'paypal_sandbox_email'                 => '',
   'sandbox_machine_ip_address'           => '123.45.6.78',

// ------- Autoresponders Settings
   'mailchimp_api_key'                    => "",
   'mailchimp_interest_groups'            => "",
   '1shoppingcart_merchant_id_number'     => "",
   'autoresponder_assignments'            => array(),

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
   'ecwid_affiliate_integration_enabled'  => '0',
   'ecwid_affiliate_tracking_detailed'    => '1',

// ------- Integration with Other Systems
   // ---- Universal Integration with Paypal shopping carts and payment systems.
   'universal_paypal_integration_enabled' => "0",
   'universal_paypal_postback_url'        => '',      // Will be dynamically initialized inside of get_settings() call
   'universal_paypal_include_file'        => '',      // Will be dynamically initialized inside of get_settings() call

   // ---- Authorize.net
   'authnet_postback_integration_enabled' => "0",
   'authnet_post_url'                     => $g_MWX__plugin_directory_url . '/extensions/Authorize.net/post-authorize-net.php', // Note: it is forced defaulted every time settings are loaded.

   // ---- E-Junkie.com
   'ejunkie_ipn_url'                      => $g_MWX__plugin_directory_url . '/mwx-notify-ejunkie.php',  // Note: it is forced defaulted every time settings are loaded.

   // ---- 2Checkout.com
   '2co_ipn_url'                          => $g_MWX__plugin_directory_url . '/mwx-notify-2co.php',       // Note: it is forced defaulted every time settings are loaded.

   // ---- ClickBank
   'clickbank_ipn_url'                    => $g_MWX__plugin_directory_url . '/mwx-notify-clickbank.php', // Note: it is forced defaulted every time settings are loaded.
   'clickbank_secret_key'                 => '',
   'clickbank_product_keyword'            => '',

   // ---- iDevAffiliate
   'idevaffiliate_integration_enabled'    => "0",
   'idevaffiliate_install_dirname'        => get_bloginfo ('wpurl') . '/idevaffiliate',

   // ---- InfusionSoft
   'infusionsoft_postback_integration_enabled' => "0",
   'infusionsoft_post_url'                => $g_MWX__plugin_directory_url . '/extensions/InfusionSoft/post.php?item_name=My+Gold+Membership',

   // ---- NMI Payments
   'nmi_integration_enabled'              => "0",
   'nmi_finish_url'                       => $g_MWX__plugin_directory_url . '/extensions/nmi-payments/nmi.php',
   'nmi_thank_you_page_url'               => get_bloginfo ('wpurl'),
   'nmi_security_key_id'                  => "0000000",

   // ---- Pay4me Payments
   'pay4me_integration_enabled'           => "0",
   'pay4me_deployment'					  => "testing",
   'pay4me_response_url'                  => $g_MWX__plugin_directory_url . '/mwx-notify-pay4me.php',
   'pay4me_merchant_code'			  	  => "",
   'pay4me_merchant_key'			  	  => "",
   'pay4me_merchant_service_id'			  => "",

   // ---- PlugNPay
   'pnp_integration_enabled'              => "0",
   'pnp_notification_url'                 => $g_MWX__plugin_directory_url . '/mwx-notify-plugnpay.php',
   'pnp_password_file_dir'                => rtrim(str_replace ('\\', '/', ABSPATH), '/') . '/PREMIUM_FILES/CHANGE_IT__TO_REAL_DIR !!!',   // Non trail-slashed name of directory
   'mwx_disable_all_emails'               => "0",

// ------- Non-UI-ed settings.
   'secret_password'                      => substr(md5(microtime()), -16), // Also used to protect custom data and prevent spoofing.
   'memberwing-x-license-info'            => array ('license_status'=>'empty', 'license_substatus'=>'disallowed', 'license_valid_until'=>'', 'is_tsi'=>FALSE, 'is_sponsored'=>TRUE, 'deal_code'=>'', 'licensee'=>'', 'message'=>'Enter valid license code to unlock all features.', 'brd'=>'<div align="center" style="font-size:9px;line-height:9px;padding:1px;margin:1px 0;border:1px solid #bbb;">Powered by <a href="{{{MEMBERWING_URL}}}" style="color:inherit;text-decoration:none;">wordpress membership plugin</a> <a href="{{{MEMBERWING_URL}}}" style="color:inherit;text-decoration:none;">MemberWing-X</a></div>'),
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
//
// Save/overwrite new user's data in cache
function MWX__save_new_user_data_in_cache ($new_user_email, $new_user_data)
{
   $new_users_cache = get_option ('MemberWing-X-new-users-cache');
   if (empty($new_users_cache) || !is_array($new_users_cache))
      $new_users_cache = array();

   $new_users_cache[$new_user_email] = $new_user_data;
   update_option ('MemberWing-X-new-users-cache', $new_users_cache);
}
//===========================================================================

//===========================================================================
//
// Return new user's cached data and optionally remove it from cache
function MWX__remove_new_user_data_from_cache ($new_user_email, $keep_in_cache=FALSE)
{
   $new_users_cache = get_option ('MemberWing-X-new-users-cache');
   if (empty($new_users_cache) || !is_array($new_users_cache) || !isset($new_users_cache[$new_user_email]))
      return FALSE;

   $new_user_data = $new_users_cache[$new_user_email];
   if (!$keep_in_cache)
      {
      unset ($new_users_cache[$new_user_email]);
      update_option ('MemberWing-X-new-users-cache', $new_users_cache);
      }

   return $new_user_data;
}
//===========================================================================

//===========================================================================
function MWX__get_settings ()
{
   global   $g_MWX__config_defaults;
   global   $g_MWX__plugin_directory_url;
   $settings_updated = FALSE;

   $mwx_settings = get_option ('MemberWing-X');

   // PHP 8: get_option() returns false when the option is missing - writing into
   // false is deprecated, and unmerged defaults cause "undefined array key" noise.
   // Same defaults-underlay merge as MWX__activated().
   if (!is_array($mwx_settings))
      $mwx_settings = array();
   if (is_array($g_MWX__config_defaults))
      $mwx_settings = array_merge ($g_MWX__config_defaults, $mwx_settings);

   if (isset($mwx_settings['premium_content_warning']))
      $mwx_settings['premium_content_warning'] = MWX__base64_decode ($mwx_settings['premium_content_warning']);

   if (isset($mwx_settings['welcome_email_body']))
      $mwx_settings['welcome_email_body']      = MWX__base64_decode ($mwx_settings['welcome_email_body']);

   //------------------------------------------------------------------------
   // Load forced-default settings
   $mwx_settings['paypal_ipn_url']                 = $g_MWX__config_defaults['paypal_ipn_url'];
   $mwx_settings['paypal_integration_code_html']   = $g_MWX__config_defaults['paypal_integration_code_html'];
   $mwx_settings['universal_paypal_postback_url']  = $g_MWX__config_defaults['paypal_ipn_url'] . "?mwx_api_key=" . $mwx_settings['mwx_api_key'] . "&skip_postback=1";
   $mwx_settings['universal_paypal_include_file']  = dirname(__FILE__) . '/mwx-notify-paypal2.php';
   $mwx_settings['authnet_post_url']               = $g_MWX__plugin_directory_url . '/extensions/Authorize.net/post-authorize-net.php';
   $mwx_settings['ejunkie_ipn_url']                = $g_MWX__plugin_directory_url . '/mwx-notify-ejunkie.php' . "?mwx_api_key=" . $mwx_settings['mwx_api_key'];
   $mwx_settings['2co_ipn_url']                    = $g_MWX__plugin_directory_url . '/mwx-notify-2co.php';
   $mwx_settings['clickbank_ipn_url']              = $g_MWX__plugin_directory_url . '/mwx-notify-clickbank.php';
   $mwx_settings['pnp_notification_url']           = $g_MWX__plugin_directory_url . '/mwx-notify-plugnpay.php';
   //------------------------------------------------------------------------

   return ($mwx_settings);
}
//===========================================================================

//===========================================================================
// Safe replacement for maybe_unserialize(get_usermeta(...)) call sites.
// Always returns an array: missing/corrupt meta comes back as array()
// instead of '' (which is a fatal TypeError on array access under PHP 8).
function MWX__get_usermeta_array ($user_id, $meta_key)
{
   $val = maybe_unserialize (get_user_meta ($user_id, $meta_key, true));
   return (is_array($val) ? $val : array());
}
//===========================================================================

//===========================================================================
// Recursively strip slashes from all elements of multi-nested array
function MWX__stripslashes (&$val)
{
   if (is_string($val))
      return (stripslashes($val));
   if (!is_array($val))
      return $val;

   foreach ($val as $k=>$v)
      {
      $val[$k] = MWX__stripslashes ($v);
      }

   return $val;
}
//===========================================================================

//===========================================================================
// Takes care of recursive updating
function MWX__update_individual_mwx_setting (&$mwx_current_setting, $mwx_new_setting)
{
   if (is_string($mwx_new_setting))
      $mwx_current_setting = MWX__stripslashes ($mwx_new_setting);
   else if (is_array($mwx_new_setting))  // Note: new setting may not exist yet in current setting: curr[t5] - not set yet, while new[t5] set.
      {
      // Need to do recursive
      foreach ($mwx_new_setting as $k=>$v)
         {
         if (!isset($mwx_current_setting[$k]))
            $mwx_current_setting[$k] = "";   // If not set yet - force set it to something.
         MWX__update_individual_mwx_setting ($mwx_current_setting[$k], $v);
         }
      }
   else
      $mwx_current_setting = $mwx_new_setting;
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
         {
         if (!isset($mwx_settings[$k]))
            $mwx_settings[$k] = ""; // Force set to something.
         MWX__update_individual_mwx_setting ($mwx_settings[$k], $_POST[$k]);
         }
      // If not in POST - existing will be used.
      }

   ///DW
   // update autoresponder assignments
   if (
      isset($_POST['new_autoresponder_level']) &&
      isset($_POST['new_autoresponder_list']) &&
      isset($_POST['new_autoresponder_service']) &&
      $_POST['new_autoresponder_level'] != ''
      && $_POST['new_autoresponder_list'] != ''
      )
      {
      // create key for this assignment
      $assignment_key = md5($_POST['new_autoresponder_level'].$_POST['new_autoresponder_list'].$_POST['new_autoresponder_service']);
      // build array
      $assignment =
         array(
            "level"     => $_POST['new_autoresponder_level'],
            "list"      => $_POST['new_autoresponder_list'],
            "service"   => $_POST['new_autoresponder_service'],
            "key"       => $assignment_key
            );
      // append to existing array
      if (!isset($mwx_settings['autoresponder_assignments']))
         $mwx_settings['autoresponder_assignments'] = array();

      array_push ($mwx_settings['autoresponder_assignments'], $assignment);
      }

   // check for deleted autoresponder assignments
   $new_assignment_array = array();
   // cycle through all autoresponder assignments and when I find the right one, remove it
   foreach ((array)@$mwx_settings['autoresponder_assignments'] as $autoresponder_assignment)
      {
      if (!isset($_POST['delete_autoresponder_assignment_'.$autoresponder_assignment['key']])) array_push($new_assignment_array, $autoresponder_assignment);
      }
   $mwx_settings['autoresponder_assignments'] = $new_assignment_array;
   ///DW/

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

   if ((float)@$mwx_settings['aff_payout_percents'] < 5)
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


   //---------------------------------------
   // Update Digital Objects Data settings
   if (isset($_POST['digital_objects']) && is_array($_POST['digital_objects']))
      {
      foreach ($_POST['digital_objects'] as $digital_object_info)
         {
         $RetCode = MWX__DB_UpdateCreate_Digital_Object ($digital_object_info);
         }
      }
   //---------------------------------------
}
//===========================================================================

//===========================================================================
//
// Reset settings only for one screen
function MWX__reset_partial_settings ()
{
   global   $g_MWX__config_defaults;

   // Load current settings and overwrite ones that are present on submitted form with defaults
   $mwx_settings = MWX__get_settings();

   foreach ($_POST as $k=>$v)
      {
      if (isset($g_MWX__config_defaults[$k]))
         {
         if (!isset($mwx_settings[$k]))
            $mwx_settings[$k] = ""; // Force set to something.
         MWX__update_individual_mwx_setting ($mwx_settings[$k], $g_MWX__config_defaults[$k]);
         }
      }

   update_option ('MemberWing-X', $mwx_settings);

   MWX__Validate_License ($mwx_settings['memberwing-x-license_code']);
}
//===========================================================================

//===========================================================================
function MWX__reset_all_settings ()
{
   global   $g_MWX__config_defaults;

   update_option ('MemberWing-X', $g_MWX__config_defaults);

   MWX__Validate_License ($g_MWX__config_defaults['memberwing-x-license_code']);
}
//===========================================================================

//===========================================================================
// Returns associative array of products.
// Replaces direct call: $products_purchased = maybe_unserialize(get_usermeta ($user_id, 'mwx_purchases'));
function MWX__GetListOfProductsForUser ($user_id)
{
   $products_purchased = MWX__get_usermeta_array ($user_id, 'mwx_purchases');

   if (!is_array($products_purchased))
      {
      $products_purchased = array();
      $update_products = true;
      }
   else
      {
      $update_products = false;
      $now_time = strtotime("now");

      foreach ($products_purchased as $idx=>$product)
         {
         // This is an attempt to autofix possibly corrupted 'mwx_purchases' metadata (leftover from previous bug)
         if (!is_array($product))
            {
            $update_products = true;

            $maybe_unserialized_product = maybe_unserialize($product);
            if (!is_array($maybe_unserialized_product) || !isset($maybe_unserialized_product['product_status']))
               {
               // Earlier versions of MWX stored corrupted values in it. Fix them
               unset ($products_purchased[$idx]);
               }
            else
               {
               $products_purchased[$idx] = $maybe_unserialized_product;
               }
            }

         if (isset($products_purchased[$idx]))
            {
            $product = $products_purchased[$idx];

            if (MWX__is_product_active($product['product_status']))
               {
               if (@$product['expiry_date'] && ($now_time > strtotime($product['expiry_date'])))
                  {
                  // Product marked as 'active' just expired. Force-Mark it as expired.
                  $products_purchased[$idx]['product_status'] = "expired";
                  $update_products = true;
                  }
               }
            else
               continue;   // Skip inactive products.
            }
         }
      }

   if ($update_products)
      {
      $products_purchased = array_values ($products_purchased);   // Reindex array.
      if (empty($products_purchased))
         {
         // update_user_meta() won't do anything for newly empty array
         delete_user_meta ($user_id, 'mwx_purchases');
         }
      update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
      }

   return ($products_purchased);
}
//===========================================================================

//===========================================================================
// Converts extension WEB URL to it's physical name
function MWX__convert_extension_url_to_filename ($url)
{
   $url = preg_replace ('|\?.*|',              '',   $url);  // Cut off query string part
   $url = preg_replace ('|.*?((/[^/]+){3})$|', "$1", $url);  // Get name part off current directory
   $url = dirname (__FILE__) . $url;                         // Construct full name
   $url = str_replace ('\\', '/', $url);                     // Fix windows slashes if any
   return ($url);
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
         wp_get_current_user();
         $user_id = $current_user->ID;
         }
      }

   if ($user_id === FALSE)
      {
      echo '<div align="center" style="margin:20px;padding:10px;border:2px solid red;">Cannot determine user_id. Time to upgrade Wordpress?</div>';
      return;
      }

   $user_information_table_html  = MWX__GetUserInformationTableHTML     ($user_id);
   $products_table_html          = MWX__GetProductsTableHTML            ($user_id);
   $aff_info_table_html          = MWX__GetAffiliateInfoTableTableHTML  ($user_id);
   $ip_prot_table_html           = MWX__GetIPProtectionInfoTableTableHTML ($user_id);

   echo '<div align="center" style="border:2px solid #F88;margin:10px 0;padding:4px;background-color:#FFE;"><span style="font-size:140%;color:#21759B;"><span style="font-weight:bold;">M</span>ember<span style="font-weight:bold;">W</span>ing-<span style="color:#F22;font-weight:bold;">X</span></span><div style="margin-top:4px;font-weight:bold;font-size:100%;background-color:#EEE;">User, Products and Affiliate Information</div></div>';
   echo $user_information_table_html;
   echo $products_table_html;

   if ($mwx_settings['mwx_affiliate_network_enabled'])
      echo $aff_info_table_html;

   echo $ip_prot_table_html;
}
//===========================================================================

//===========================================================================
function MWX__update_user_profile ($user_id=FALSE)
{
   if (!MWX__is_user_admin())
       return FALSE;

   if (!$user_id)
      {
      if (isset($_REQUEST['user_id']))
         $user_id = $_REQUEST['user_id'];
      }
   if ($user_id === FALSE)
      {
      global $current_user;
      wp_get_current_user();
      $user_id = $current_user->ID;
      }
   if ($user_id === FALSE)
      {
      echo '<div align="center" style="margin:20px;padding:10px;border:2px solid red;">Cannot determine user_id. Time to upgrade Wordpress?</div>';
      return;
      }

   //---------------------------------------
   // Update purchases information
   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

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
            $products_purchased[$prod_idx]['expiry_date']    = $prod_inputs['expiry_date'];
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
            'purchase_date'   => date ('Y-m-d H:i:s', strtotime ("now")),
            'expiry_date'     => $_POST['new_product']['expiry_date'],
            'txn_ids'         => array("Manual by admin"),
            'subscr_id'       => $_POST['new_product']['subscr_id'],
            'referred_by_id'  => 'self',        // Affiliate's id (user_id) who refered this purchase
            'product_status'  => 'active',      // 'active'(customer is in good standing), 'cancelled'(subscription), 'refunded'(one of payments was refunded), 'deactivated'(if refund happened or manually set by admin)
            );
      }

   update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
   //---------------------------------------

   //---------------------------------------
   // Update affiliate information
   $mwx_aff_info = MWX__get_usermeta_array ($user_id, 'mwx_aff_info');
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

         $mwx_aff_info['referrals'] = array_values (is_array(@$mwx_aff_info['referrals']) ? $mwx_aff_info['referrals'] : array());   // Reindex array.
         }

      update_user_meta ($user_id, 'mwx_aff_info', serialize ($mwx_aff_info));
      }
   //---------------------------------------

   //---------------------------------------
   // Update IP protection information

	$user_ip_data = MWX__get_usermeta_array ($user_id, 'user_ip_data');
	if (is_array($user_ip_data))
	{
		$registered_ips = is_array(@$user_ip_data['registered_ips']) ? $user_ip_data['registered_ips'] : array();
	}
	else
	{
		$user_ip_data = array();
		$registered_ips = array();
	}

// PRODS: <td><div align="center"><input type="checkbox" style="float:none;" value="1" name="products[{PROD_IDX}][delete]" /></div></td>
// IPROT: <td><div align="center"><input type="checkbox" style="float:none;" value="1" name="registered_ips[{REGIP_IDX}][delete]" /></div></td>

	if (isset($_POST['registered_ips']) && is_array ($_POST['registered_ips']))
	{
		$regips_modified = false;
	  foreach ($_POST['registered_ips'] as $regip_idx=>$regip_inputs)
	     {
	     if (isset($regip_inputs['delete']) && $regip_inputs['delete'])
	        {
	        unset ($registered_ips[$regip_idx]);
	        $regips_modified = true;
	        }
	     }

	  if ($regips_modified)
	  {
	  	$registered_ips = array_values ($registered_ips);   // Reindex array.
	  	$user_ip_data['registered_ips'] = $registered_ips;
			update_user_meta ($user_id, 'user_ip_data', serialize ($user_ip_data));
		}
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
function MWX__GetUserInformationTableHTML ($user_id)
{
   if (!MWX__is_user_admin())
      {
      return "";  // Only admins can see this
      }

   $mwx_extra_user_data = MWX__get_usermeta_array ($user_id, 'mwx_extra_user_data');
   $invitation_code = @$mwx_extra_user_data['invitation_code'];

   if ($invitation_code)
      $invitation_code = "<b style=\"color:red;\">$invitation_code</b> ({$mwx_extra_user_data['invitation_code_status']})";
   else
      $invitation_code = "<i>not present</i>";

   $registration_date    = @$mwx_extra_user_data['registration_date']?$mwx_extra_user_data['registration_date']:"<i>undefined</i>";
   $registration_ip_addr = @$mwx_extra_user_data['registration_ip_addr']?$mwx_extra_user_data['registration_ip_addr']:"<i>undefined</i>";

   if (@$mwx_extra_user_data['referred_by_id'])
      {
      $user_info = get_userdata ($mwx_extra_user_data['referred_by_id']);
      $referred_by = @$user_info->user_login;
      if (!$referred_by)
         $referred_by = $mwx_extra_user_data['referred_by_id'] . " (<i>?</i>)";
      }
   else
      $referred_by = "<i>none</i>";

   $table_html =<<<TTT
   <div align="center" style="border:2px solid gray;margin:3px 0;padding:5px;background-color:#FFE;">
      <b>Used invitation code</b>: $invitation_code
         ,&nbsp;&nbsp;&nbsp;
      <b>Registration date</b>: $registration_date
         ,&nbsp;&nbsp;&nbsp;
      <b>Registration IP</b>: $registration_ip_addr
         ,&nbsp;&nbsp;&nbsp;
      <b>Referred by</b>: $referred_by
   </div>
TTT;

   $mwx_extra_user_data['registration_date']    = date ("Y-m-d H:i:s T", strtotime("now"));
   $mwx_extra_user_data['registration_ip_addr'] = @$_SERVER['REMOTE_ADDR'];

   return $table_html;
}
//===========================================================================

//===========================================================================
//
// Adds table with the list of products owned by this user to user's profile.

function MWX__GetProductsTableHTML ($user_id)
{
   // array (array('product_id'=>'5', 'product_name'=>'', 'date'=>'2009-12-02', 'txn_id'=>array(...), 'subscr_id'=>'', 'active'=>'1'), array(...), ...)
   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

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
      <td style="background-color:#B5FFA8;" width="55%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Product name</strong></div></td>
      <td style="background-color:#B5FFA8;" width="12%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Expiry date</strong></div></td>
      <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Status?</strong></div></td>
      <td style="background-color:#FF9D9F;" width="8%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; font-weight: bold;">Delete!</div></td>
   </tr>
   {TABLE_ROWS}
  <tr>
    <td  style="background-color:#FBFFB3;"colspan="6"><div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 12px;padding:6px 0;"><strong>Add new product for this user:</strong></div></td>
  </tr>
  <tr>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[product_id]"   value="" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[subscr_id]"   value="" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[product_name]" value="" class="regular-text" size="100" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text"     name="new_product[expiry_date]" value="" class="regular-text" size="12" /></div></td>
      <td colspan="2" style="background-color:gray;"><div align="center"></div></td>
  </tr>
</table>
TTT;

$product_row_admin =<<<TTT
   <tr>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_id]" value="{PRODUCT_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][subscr_id]" value="{SUBSCR_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_name]" value="{PRODUCT_NAME}" readonly="readonly" class="regular-text" size="100"/></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][expiry_date]" value="{EXPIRY_DATE}" class="regular-text" size="12"/></div></td>
      <td style="background-color:white;"><div align="center">{PRODUCT_STATUS_SELECT_HTML}</div></td>
      <td style="background-color:white;"><div align="center"><input type="checkbox" style="float:none;" value="1" name="products[{PROD_IDX}][delete]" /></div></td>
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
      <td style="background-color:#B5FFA8;" width="68%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Product name</strong></div></td>
      <td style="background-color:#B5FFA8;" width="12%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Expiry date</strong></div></td>
   </tr>
   {TABLE_ROWS}
</table>
TTT;

$product_row_user =<<<TTT
   <tr>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_id]" value="{PRODUCT_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][subscr_id]" value="{SUBSCR_ID}" readonly="readonly" class="regular-text" /></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][product_name]" value="{PRODUCT_NAME}" readonly="readonly" class="regular-text" size="100"/></div></td>
      <td style="background-color:white;"><div align="center"><input type="text" name="products[{PROD_IDX}][expiry_date]" value="{EXPIRY_DATE}" readonly="readonly" class="regular-text" size="12"/></div></td>
   </tr>
TTT;
//------------------------------------------

$no_products_row = '<tr><td style="background-color:#FBFFB3;" colspan="6"><div align="center" style="font-family: Georgia, Times, serif;font-size: 12px;padding:6px 0;"><strong>No products currently owned</strong></div></td></tr>';

   if (MWX__is_user_admin())
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
         if (!$is_admin && !MWX__is_product_active($product['product_status']))
            continue; // Do not show inactive products to regular users.

         $next_row = $product_row;
         $next_row = preg_replace ('@\{PROD_IDX\}@',           $idx, $next_row);
         $next_row = preg_replace ('@\{PRODUCT_ID\}@',         $product['product_id'], $next_row);
         $next_row = preg_replace ('@\{SUBSCR_ID\}@',          $product['subscr_id'], $next_row);
         $next_row = preg_replace ('@\{PRODUCT_NAME\}@',       $product['product_name'], $next_row);
         $next_row = preg_replace ('@\{EXPIRY_DATE\}@',       $product['expiry_date'], $next_row);

         //------------------------------------
         // Calc product status HTML
         // 'active'(customer is in good standing), 'active-ending'(subscr cancelled before end of term), 'cancelled'(subscription), 'ended'(subscription ended normally), 'expired'(forced expiry date reached), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
         $sel_msg = array('active'=>'', 'active-ending'=>'', 'cancelled'=>'', 'ended'=>'', 'expired'=>'', 'refunded'=>'', 'deactivated'=>'');
         $sel_msg[$product['product_status']] = 'selected="selected"';
         $product_status_html =<<<TTT
<select name="products[$idx][product_status]" size="1">
           <option value="active"         {$sel_msg['active']}>active</option>
           <option value="active-ending"  {$sel_msg['active-ending']}>active-ending</option>
           <option value="cancelled"      {$sel_msg['cancelled']}>cancelled</option>
           <option value="ended"          {$sel_msg['ended']}>ended</option>
           <option value="expired"        {$sel_msg['expired']}>expired</option>
           <option value="refunded"       {$sel_msg['refunded']}>refunded</option>
           <option value="deactivated"    {$sel_msg['deactivated']}>deactivated</option>
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
//
// Adds table with the list of products owned by this user to user's profile.

function MWX__GetIPProtectionInfoTableTableHTML ($user_id)
{

   if (!MWX__is_user_admin())
   		return ""; // Must be admin


   ///// array (array('product_id'=>'5', 'product_name'=>'', 'date'=>'2009-12-02', 'txn_id'=>array(...), 'subscr_id'=>'', 'active'=>'1'), array(...), ...)
   ///$products_purchased = MWX__GetListOfProductsForUser ($user_id);

  $mwx_settings = MWX__get_settings();

	$user_ip_data = MWX__get_usermeta_array ($user_id, 'user_ip_data');
	if (is_array($user_ip_data))
	{
		$registered_ips = is_array(@$user_ip_data['registered_ips']) ? $user_ip_data['registered_ips'] : array();
		$log_history = is_array(@$user_ip_data['log_history']) ? $user_ip_data['log_history'] : array();
	}
	else
	{
		$registered_ips = array();
		$log_history = array();
	}
	// $current_ip_info = array(
	// 	'datetimestamp' 		=> date ('Y-m-d H:i:s T', strtotime ("now")),
	// 	'REMOTE_ADDR'   		=> MWX__get_visitor_REMOTE_ADDR (),
	// 	'HTTP_USER_AGENT'		=> $_SERVER['HTTP_USER_AGENT']
	// 	)


//------------------------------------------
// Admin view of registered IPs table

	if ($mwx_settings['ip_protection_enabled'])
	{
		$ip_prot_disabled_message = "";
	}
	else
	{
		$ip_prot_disabled_message = "<br />NOTE: <b style='color:#A00;'>IP Protection is currently disabled</b>. You may enable it from MemberWing General Settings area.";
	}

$table_html_admin =<<<TTT
<table style="background-color:#555;margin-top:20px;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 20px;margin:0 0 10px;background:#FFD;color:#21759B;line-height:32px;" colspan="5"><div align="center">Registered IP Addresses (maximum: {$mwx_settings['ip_protection_max_allowed_addresses']}) user is allowed to login from (unique IP addresses user first used to login from){$ip_prot_disabled_message}</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="12%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Date used first</strong></div></td>
      <td style="background-color:#B5FFA8;" width="10%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>IP Address</strong></div></td>
      <td style="background-color:#B5FFA8;" width="5%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Times used</strong></div></td>
      <td style="background-color:#B5FFA8;" width="67%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Browser identifier</strong></div></td>
      <td style="background-color:#FF9D9F;" width="6%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; font-weight: bold;">Delete!</div></td>
   </tr>
   {TABLE_ROWS}
</table>
TTT;

$registered_ip_addr_row_admin =<<<TTT
   <tr>
      <td style="background-color:white;" width="12%"><div align="center">{REGIPVAL_datetimestamp}</div></td>
      <td style="background-color:white;" width="10%"><div align="center"><a target="_blank" href="http://myip.ms/info/whois/{REGIPVAL_REMOTE_ADDR}">{REGIPVAL_REMOTE_ADDR}</a></div></td>
      <td style="background-color:white;" width="5%"><div align="center">{REGIPVAL_count}</div></td>
      <td style="background-color:white;" width="67%"><div align="center">{REGIPVAL_HTTP_USER_AGENT}</div></td>
      <td style="background-color:white;" width="6%"><div align="center"><input type="checkbox" style="float:none;" value="1" name="registered_ips[{REGIP_IDX}][delete]" /></div></td>
   </tr>
TTT;

$no_regips_row = '<tr><td style="background-color:#FBFFB3;" colspan="5"><div align="center" style="font-family: Georgia, Times, serif;font-size: 12px;padding:6px 0;"><strong>No registered IP addresses in the list (IP Protection is disabled or user never logged in)</strong></div></td></tr>';
//------------------------------------------

//------------------------------------------
// Admin view of IP log table
$log_table_html_admin =<<<TTT
<table style="background-color:#555;margin-top:20px;" width="100%" border="1">
   <tr>
      <td style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 20px;margin:0 0 10px;background:#FFD;color:#21759B;line-height:32px;" colspan="3"><div align="center">Log of last {$mwx_settings['ip_protection_log_history_size']} logins</div></td>
   </tr>
   <tr>
      <td style="background-color:#B5FFA8;" width="18%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Date/time</strong></div></td>
      <td style="background-color:#B5FFA8;" width="12%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>IP Address</strong></div></td>
      <td style="background-color:#B5FFA8;" width="70%"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;"><strong>Browser identifier</strong></div></td>
   </tr>
   {TABLE_ROWS}
</table>
TTT;

$log_table_row_admin =<<<TTT
   <tr>
      <td style="background-color:white;padding:0;" width="18%"><div style="font-size:11px;margin:0;padding:0;" align="center">{REGIPVAL_datetimestamp}</div></td>
      <td style="background-color:white;padding:0;" width="12%"><div style="font-size:11px;margin:0;padding:0;" align="center"><a target="_blank" href="http://myip.ms/info/whois/{REGIPVAL_REMOTE_ADDR}">{REGIPVAL_REMOTE_ADDR}</a></div></td>
      <td style="background-color:white;padding:0;" width="70%"><div style="font-size:11px;margin:0;padding:0;" align="center">{REGIPVAL_HTTP_USER_AGENT}</div></td>
   </tr>
TTT;

$no_logs_row = '<tr><td style="background-color:#FBFFB3;" colspan="3"><div align="center" style="font-family: Georgia, Times, serif;font-size: 12px;padding:6px 0;"><strong>Log is empty. No user logins were detected</strong></div></td></tr>';
//------------------------------------------

	$final_html = "";

	//---------------------------------------
	$table_html  = $table_html_admin;
	$registered_ip_row = $registered_ip_addr_row_admin;

	if (count($registered_ips))
	{
	  $registered_ip_rows="";
	  foreach ($registered_ips as $idx=>$registered_ip_entry)
	  {
	     $next_row = $registered_ip_row;
	     $next_row = preg_replace ('@\{REGIP_IDX\}@',           $idx, $next_row);
	     $next_row = preg_replace ('@\{REGIPVAL_datetimestamp\}@',         $registered_ip_entry['datetimestamp'], $next_row);
	     $next_row = preg_replace ('@\{REGIPVAL_REMOTE_ADDR\}@',          $registered_ip_entry['REMOTE_ADDR'], $next_row);
	     $next_row = preg_replace ('@\{REGIPVAL_count\}@',          $registered_ip_entry['count'], $next_row);
	     $next_row = preg_replace ('@\{REGIPVAL_HTTP_USER_AGENT\}@',       $registered_ip_entry['HTTP_USER_AGENT'], $next_row);

	     $registered_ip_rows .= $next_row;
	  }

  	$table_html = preg_replace ('@\{TABLE_ROWS\}@', $registered_ip_rows, $table_html);
	}
	else
	{
	  $table_html = preg_replace ('@\{TABLE_ROWS\}@', $no_regips_row, $table_html);
	}
	$final_html .= $table_html;
	//---------------------------------------

	//---------------------------------------
	$table_html  = $log_table_html_admin;
	$log_entry_row = $log_table_row_admin;

	if (count($log_history))
	{
	  $log_entry_rows="";
	  foreach ($log_history as $idx=>$log_entry)
	  {
	     $next_row = $log_entry_row;
	     $next_row = preg_replace ('@\{REGIPVAL_datetimestamp\}@',         $log_entry['datetimestamp'], $next_row);
	     $next_row = preg_replace ('@\{REGIPVAL_REMOTE_ADDR\}@',          $log_entry['REMOTE_ADDR'], $next_row);
	     $next_row = preg_replace ('@\{REGIPVAL_HTTP_USER_AGENT\}@',       $log_entry['HTTP_USER_AGENT'], $next_row);

	     $log_entry_rows .= $next_row;
	  }

  	$table_html = preg_replace ('@\{TABLE_ROWS\}@', $log_entry_rows, $table_html);
	}
	else
	{
	  $table_html = preg_replace ('@\{TABLE_ROWS\}@', $no_logs_row, $table_html);
	}
	$final_html .= $table_html;
	//---------------------------------------

	return $final_html;
}
//===========================================================================

//===========================================================================
function MWX__GetAffiliateInfoTableTableHTML ($user_id)
{
//   if ($user->user_level==10)
//      return '';
   $mwx_settings = MWX__get_settings();

   // Get information about user as affiliate
   $mwx_aff_info = MWX__get_usermeta_array ($user_id, 'mwx_aff_info');

   //----------------------------------------------------------
   // If affiliate info does not exist - recreate it.
   if (!is_array($mwx_aff_info) || !count($mwx_aff_info))
      {
      $mwx_aff_info = MWX__Generate_Default_Affiliate_Metadata (FALSE, $mwx_settings);
      update_user_meta ($user_id, 'mwx_aff_info', serialize ($mwx_aff_info));
      }
   //----------------------------------------------------------

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

   if (!MWX__is_user_admin())
      {
      return $aff_info_html;  ///!!! no aff info yet for non-admins
      }
   // ***********************************************************************


   // ***********************************************************************
   // ***** Build "affiliate account information" table *********************
   // ***********************************************************************
   if (!isset($mwx_aff_info['aff_status']))
      {
      $aff_account_info_table_html_admin = "";
      }
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
         $aff_immune_html = '<input type="hidden" name="immune_to_min_payout_limit" value="0" /><input type="checkbox" style="float:none;" value="1" name="immune_to_min_payout_limit" checked="checked" />';
      else
         $aff_immune_html = '<input type="hidden" name="immune_to_min_payout_limit" value="0" /><input type="checkbox" style="float:none;" value="1" name="immune_to_min_payout_limit" />';
      //------------------------------------
      //------------------------------------
      // Calc payout % HTML
      if (!$mwx_aff_info['payout_percents'])
         $mwx_aff_info['payout_percents'] = $mwx_settings['aff_payout_percents'];
      $payout_percents_html = '<input type="text" name="payout_percents" value="' . $mwx_aff_info['payout_percents'] . '" size="3" />%';
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
         // $referral_row_html .= '<td style="background-color:white;"><div align="center"><input type="checkbox" style="float:none;" value="1" name="delete_aff_referral[' . $ref_index . ']" /></div></td>';
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
//{{{PHP_MELT}}}
// This function runs upon plugin activation or when webmaster pressed "Update settings" or "Validate License" button.
// If license is valid - returns array of license information for the given 'license_code'. Otherwise returns FALSE.
//{{{/PHP_MELT}}}

// Synchronize database records

//{{{PHP_DO_NOT_ENCODE}}}
function MWX__Validate_License ($license_code)
{
   global   $g_MWX__config_defaults;

   // Load current settings
   $mwx_settings = MWX__get_settings ();

   // Offline validation: keys are Ed25519-signed by the MemberWing license tool
   // and verified locally against MWX_LICENSE_PUBLIC_KEY. The old
   // memberwing.com/LICENSE_VALIDATOR server is retired - nothing phones home.
   $license_code = trim ((string)$license_code);
   $LV__license_info = MWX__Parse_License_Key ($license_code);

   $mwx_settings['memberwing-x-license_code'] = $license_code;
   $mwx_settings['memberwing-x-license-info'] = $LV__license_info;

   if (isset($LV__license_info['brd']) && $LV__license_info['brd'])
      {
      if (trim((string)@$mwx_settings['your-memberwing-affiliate-link']))
         {
         $mwx_settings['memberwing-x-license-info']['brd'] = str_replace ('{{{MEMBERWING_URL}}}', trim($mwx_settings['your-memberwing-affiliate-link']), $LV__license_info['brd']);
         }
      else
         {
         $mwx_settings['memberwing-x-license-info']['brd'] = str_replace ('{{{MEMBERWING_URL}}}', 'http://www.mensk.com/', $LV__license_info['brd']);
         }
      }

   MWX__update_settings ($mwx_settings);

   return ($LV__license_info);
}
//{{{/PHP_DO_NOT_ENCODE}}}
//===========================================================================

//===========================================================================
//{{{PHP_DO_NOT_ENCODE}}}
// Ed25519 public key matching the private key held by the license generator tool.
define ('MWX_LICENSE_PUBLIC_KEY', '+fK6e3feokQsWQpax+AY5so0LSJB8hHnDbzcef3nElA=');

// Parses + verifies an offline license key of the form MWX1.<payload>.<signature>.
// Always returns a fully-populated license-info array (every key present).
function MWX__Parse_License_Key ($license_code)
{
   $info = array (
      'license_status'      => 'empty',
      'license_substatus'   => 'disallowed',
      'license_valid_until' => '',
      'is_tsi'              => FALSE,
      'is_sponsored'        => TRUE,
      'deal_code'           => '',
      'licensee'            => '',
      'message'             => 'Enter valid license code to unlock all features.',
      'brd'                 => '<div align="center" style="font-size:9px;line-height:9px;padding:1px;margin:1px 0;border:1px solid #bbb;">Powered by <a href="{{{MEMBERWING_URL}}}" style="color:inherit;text-decoration:none;">wordpress membership plugin</a> <a href="{{{MEMBERWING_URL}}}" style="color:inherit;text-decoration:none;">MemberWing-X</a></div>',
      );

   $license_code = trim ((string)$license_code);
   if ($license_code === '')
      return ($info);

   $parts = explode ('.', $license_code);
   if (count($parts) != 3 || $parts[0] !== 'MWX1')
      {
      $info['license_status'] = 'invalid';
      $info['message'] = 'License key is malformed. Please re-copy the entire key.';
      return ($info);
      }

   $payload = base64_decode (strtr($parts[1], '-_', '+/'));
   $sig     = base64_decode (strtr($parts[2], '-_', '+/'));
   $pubkey  = base64_decode (MWX_LICENSE_PUBLIC_KEY);

   // sodium is bundled with PHP 7.2+; WordPress 5.2+ also ships the sodium_compat polyfill.
   if (!function_exists ('sodium_crypto_sign_verify_detached'))
      {
      $info['license_status'] = 'invalid';
      $info['message'] = 'Cannot verify license: PHP sodium extension is unavailable.';
      return ($info);
      }

   $signature_ok = FALSE;
   try
      {
      $signature_ok = ($payload !== false && $sig !== false && strlen($sig) === SODIUM_CRYPTO_SIGN_BYTES && sodium_crypto_sign_verify_detached ($sig, $payload, $pubkey));
      }
   catch (Exception $e) { $signature_ok = FALSE; }
   catch (Error $e)     { $signature_ok = FALSE; }

   $data = $signature_ok ? json_decode ($payload, true) : null;
   if (!$signature_ok || !is_array($data))
      {
      $info['license_status'] = 'invalid';
      $info['message'] = 'License key is invalid.';
      return ($info);
      }

   $info['licensee'] = (string)@$data['n'];
   $info['license_valid_until'] = ((string)@$data['x'] === 'never') ? '2099-12-31' : (string)@$data['x'];

   // Expiry check
   if ((string)@$data['x'] !== 'never' && strtotime ((string)@$data['x'] . ' 23:59:59') < time())
      {
      $info['license_status'] = 'expired';
      $info['message'] = 'License key expired on ' . $data['x'] . '.';
      return ($info);
      }

   // Domain check: exact (www. and port ignored), *.domain.tld wildcard, or * for any domain.
   // Use the site's configured home URL, not HTTP_HOST: it is stable in CLI/cron
   // contexts (wp-cli plugin activation has no HTTP_HOST) and not client-controllable.
   $host = '';
   if (function_exists ('home_url'))
      $host = (string)@parse_url (home_url (), PHP_URL_HOST);
   if ($host === '')
      $host = (string)@$_SERVER['HTTP_HOST'];
   $host = strtolower ($host);
   $host = preg_replace ('/:\d+$/', '', $host);
   $host = preg_replace ('/^www\./', '', $host);

   $lic_domain = strtolower ((string)@$data['d']);
   $domain_ok = FALSE;
   if ($lic_domain === '*')
      $domain_ok = TRUE;
   else if (strpos ($lic_domain, '*.') === 0)
      {
      $bare = substr ($lic_domain, 2);      // "*.example.com" -> "example.com"
      $domain_ok = ($host === $bare || substr ($host, -strlen('.' . $bare)) === '.' . $bare);
      }
   else
      $domain_ok = ($host === preg_replace ('/^www\./', '', $lic_domain));

   if (!$domain_ok)
      {
      $info['license_status'] = 'valid';
      $info['message'] = "License key is not valid for this domain ($host). It is issued for '{$data['d']}'.";
      return ($info);
      }

   // All checks passed
   $info['license_status']    = 'valid';
   $info['license_substatus'] = 'allowed';
   $info['is_tsi']            = ((string)@$data['e'] === 'TSI');
   $info['is_sponsored']      = FALSE;
   $info['message']           = '';
   $info['brd']               = '';
   return ($info);
}
//{{{/PHP_DO_NOT_ENCODE}}}
//===========================================================================

//===========================================================================
//{{{PHP_DO_NOT_ENCODE}}}
function MWX__License_Allowed ($mwx_settings=false)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();
   return (@$mwx_settings['memberwing-x-license-info']['license_substatus'] == 'allowed');
}
//{{{/PHP_DO_NOT_ENCODE}}}
//===========================================================================

//===========================================================================
//{{{PHP_DO_NOT_ENCODE}}}
function MWX__License_TSI ($mwx_settings=false)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

   return (@$mwx_settings['memberwing-x-license-info']['is_sponsored'] || (MWX__License_Allowed ($mwx_settings) && @$mwx_settings['memberwing-x-license-info']['is_tsi']));
}
//{{{/PHP_DO_NOT_ENCODE}}}
//===========================================================================

//===========================================================================
//{{{PHP_DO_NOT_ENCODE}}}
function MWX__render_memberwing_x_license_caps ()
{
   $mwx_settings = MWX__get_settings ();
   if (MWX__License_TSI ($mwx_settings))
      return "TSI";
   else
      return "";
}
//{{{/PHP_DO_NOT_ENCODE}}}
//===========================================================================

//===========================================================================
function MWX__get_admin_license_error_message ($mwx_settings="")
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

//   $html_error_msg = '<div align="center" style="padding:5px;margin-top:10px;border:2px solid #A00;background-color:#FFA;font-weight:bold;"><span style="color:red;font-weight:bold;font-size:120%;">WARNING: MemberWing-X license key is invalid or not entered.</span><br />Digital Content Protection and Digital Online Store Builder features are disabled.<br />To enable full set of features please enter your license key at MemberWing-X settings (General Settings) or<br /><span style="font-size:125%;"><b><a href="http://www.memberwing.com/#BuyMemberWingNow" target="_blank">Instantly purchase MemberWing-X license here</a></b></span></div></td>';
   $html_error_msg = '<div align="center" style="padding:5px;margin-top:10px;border:4px solid #B00;background-color:#FFA;font-weight:bold;">' . '<span style="font-size:120%;color:red;">WARNING</span><br />' . $mwx_settings['memberwing-x-license-info']['message'] . '</div>';
   return ($html_error_msg);
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
      case 'dcp'        :           MWX__render_dcp_settings_page_html();              break;
      case 'dos'        :           MWX__render_dos_settings_page_html();              break;
      case 'categories'     :       MWX__render_categories_settings_page_html();       break;
      case 'products'     :         MWX__render_products_settings_page_html();         break;
      case 'invitation codes':      MWX__render_invitation_codes_settings_page_html(); break;
      case 'paypal'     :           MWX__render_paypal_settings_page_html();           break;
      case 'autoresponders'     :   MWX__render_autoresponders_settings_page_html();   break;
      case 'email'     :            MWX__render_email_settings_page_html();            break;
      case 'affiliate settings'  :  MWX__render_affiliate_settings_page_html();        break;
      case 'affiliate payouts'  :   MWX__render_affiliate_payouts_page_html();         break;
      case 'user management'     :  MWX__render_user_management_page_html();           break;
      case 'user profile'        :  MWX__render_user_profile_page_html();              break;
      case 'other systems'     :    MWX__render_other_systems_settings_page_html();    break;
      }

   echo '</div>';
}
//===========================================================================

//===========================================================================
function MWX__GetCurrencySymbolHTML ($mwx_settings)
{
   if (strtoupper($mwx_settings['paypal_currency_code']) == 'USD')
      return '$';
   if (strtoupper($mwx_settings['paypal_currency_code']) == 'GBP')
      return '&pound;';
   if (strtoupper($mwx_settings['paypal_currency_code']) == 'EUR')
      return '&euro;';
   return '$';
}
//===========================================================================

//===========================================================================
function MWX__render_memberwing_x_version ()
{
   $latest_available_version = "8.601"; ///!!!!!!MWX__file_get_contents ('http://www.memberwing.com/LATEST/mwx/get.php?what=latest_version_number');
   $license_caps = MWX__render_memberwing_x_license_caps();
   $license_caps = $license_caps?('<span style="background-color:#FF0;">&nbsp;' . $license_caps . '&nbsp;</span>'):"---";
?>
   <div align="center" style="border:2px solid gray;font-size:130%;margin-top:10px;padding:5px;">MemberWing-X version: <span style="color:red;font-weight:bold;">
      <?php echo MEMBERWING_X_VERSION . ' ' . $license_caps . ' ' . '(' . MEMBERWING_X_EDITION . ')'; ?></span>
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

   $premium_content_warning_premium_html  = MWX__AssemblePremiumContentWarningMessage ("12", "0", "4.95|gold", FALSE);
   $premium_content_warning_free_html     = MWX__AssemblePremiumContentWarningMessage ("12", "0", "*", FALSE);
   $premium_content_warning_delayed_html  = MWX__AssemblePremiumContentWarningMessage ("12", "0", "?:14d", 14*24*60*60);

   // Remove FORM tags to avoid MSIE confusion
   $premium_content_warning_premium_html  = preg_replace ('|form\>|', 'uniform>', $premium_content_warning_premium_html);
   $premium_content_warning_free_html     = preg_replace ('|form\>|', 'uniform>', $premium_content_warning_free_html);
   $premium_content_warning_delayed_html  = preg_replace ('|form\>|', 'uniform>', $premium_content_warning_delayed_html);

   $license_status = "<b>License status: " . $mwx_settings['memberwing-x-license-info']['license_status'] . " (" . $mwx_settings['memberwing-x-license-info']['license_substatus'] . ")</b>";

//{{{PHP_DO_NOT_ENCODE}}}
   if (@$mwx_settings['memberwing-x-license-info']['deal_code'] == 'bigappdeals1')
      {
      $bappd1=TRUE;
      $pb_is_disabled = 'disabled="disabled"';
      }
   else
      {
      $bappd1=FALSE;
      $pb_is_disabled = '';
      }
//{{{/PHP_DO_NOT_ENCODE}}}

   if (preg_match('@^(\d{4}\-|Unlim)@', (string)@$mwx_settings['memberwing-x-license-info']['license_valid_until']))
      {
      if ($mwx_settings['memberwing-x-license-info']['license_status'] == 'expired')
         $verb = "on";
      else
         $verb = "<br /><a href=\"http://toprate.org/mwxlicense\" target=\"_blank\">Free upgrades</a> are available until";
      $license_status .= " $verb: " . "<span style=\"font-weight:bold;color:red;\">{$mwx_settings['memberwing-x-license-info']['license_valid_until']}</span>";
      }


   if (@$mwx_settings['memberwing-x-license-info']['message'])
      $license_status .= '<hr />' . $mwx_settings['memberwing-x-license-info']['message'];

   if (!$mwx_settings['memberwing-x-license_code'])
      $license_message = 'Your license code for MemberWing-X<br />' . '<div align="center" style="border:2px solid red;background-color:yellow;margin:2px;padding:4px;">' . $license_status . '</div>' . '<b>Users who own MemberWing-X license are eligible for technical support and have an ability to turn off branding messages.</b><br /><div align="center" style="padding:3px;"><a href="http://www.memberwing.com/"><span align="center" style="font-weight:bold;font-size:130%;">Buy MemberWing-X License here</span></a></div>';
   else if ($mwx_settings['memberwing-x-license-info']['license_status'] == 'valid')
      $license_message = 'Your license code for MemberWing-X<br /><div align="center" style="border:2px solid green;margin:2px;padding:4px;">' . $license_status . '</div>';
   else
      $license_message = 'Your license code for MemberWing-X<br /><div align="center" style="border:2px solid red;background-color:yellow;margin:2px;padding:4px;">' . $license_status . '</div>';

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">General Settings</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFA;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
      <tr>
         <td style="background-color:#FFF;" colspan="3"><div align="center" style="padding:10px 0;"></div></td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="45%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="35%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">MemberWing-X License Code:</div></td>
         <td style="background-color:#CCC;padding-top:8px;">
            <div align="center">
               <input type="text" name="memberwing-x-license_code" value="<?php echo $mwx_settings['memberwing-x-license_code']; ?>" size="80" />
               <div align="center"><input style="padding:4px;margin-top:4px;background-color:#BEB;font-weight:bold;" type="submit" name="validate_memberwing-x-license" value="Validate MemberWing-X License" /></div>
               <div style="<?php if ($mwx_settings['memberwing-x-license-info']['is_sponsored']) echo 'display:none;'; ?>">
                  Show &quot;Powered by MemberWing-X&quot;: <input type="hidden" name="show-powered-by" value="0" /><input type="checkbox" style="float:none;" value="1" name="show-powered-by" <?php if ($mwx_settings['show-powered-by']) echo ' checked="checked" '; ?> <?php echo $pb_is_disabled; ?>/>
               </div>
<?php
//{{{PHP_DO_NOT_ENCODE}}}
   if ($bappd1)
      {
      echo <<<TTT
      <div style="border:1px solid #aa0000;margin:2px 5px;font-size:90%;padding:2px;background-color:#FFC;">To remove branding, receive support and get free yearly upgrades:<br />please <b><a target="_blank" href="http://www.memberwing.com/buy/">upgrade your license here</a></b></div>
TTT;
      }
//{{{/PHP_DO_NOT_ENCODE}}}
?>
            </div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;"><?php echo $license_message; ?></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Your memberwing affiliate link:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="your-memberwing-affiliate-link" value="<?php echo $mwx_settings['your-memberwing-affiliate-link']; ?>" size="70" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your memberwing affiliate link. Example: <b>http://www.memberwing.com/270.html</b><br />If you put your affiliate link here then "Powered by" message link will use it. This will help you to earn affiliate commissions if someone will buy MemberWing by clicking on your affiliate "Powered by" link.<br />If you don't have affiliate link yet, <a href="http://www.memberwing.com/affiliate-program/" target="_blank"><b>please signup here</b></a>. </div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Default Primary Payment Processor:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <select name="default_primary_payment_processor" size="1">
                 <option value="paypal"        <?php echo ($mwx_settings['default_primary_payment_processor']=='paypal' || !$mwx_settings['authnet_postback_integration_enabled'])?'selected="selected"':""; ?> >Paypal</option>
                 <option value="authorize.net" <?php echo (!$mwx_settings['authnet_postback_integration_enabled'])?'disabled="disabled"':''; ?> <?php echo ($mwx_settings['default_primary_payment_processor']=='authorize.net' && $mwx_settings['authnet_postback_integration_enabled'])?'selected="selected"':""; ?> >Authorize.net</option>
               </select>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Select payment processor to be used.<br />To use authorize.net as a payment processor you need to have <b><a href="http://toprate.org/auth-net" target="_blank">Authorize.net plugin</a></b> installed, activated <b>and</b> have integration with Authorize.net enabled and configured in MemberWingX-&gt;Integration with Other Systems.</div></td>
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
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">&quot;Download&quot; button image URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="download_button_image" value="<?php echo $mwx_settings['download_button_image']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;"><img src="<?php echo $mwx_settings['download_button_image']; ?>" /> URL of your custom &quot;Download&quot; image</div></td>
      </tr>
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
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{ACCESS_DELAYED_MSG}:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <textarea style="font-size:10px;" name="access_delayed_msg" cols=90 rows=2><?php echo $mwx_settings['access_delayed_msg']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Content of {ACCESS_DELAYED_MSG} variable.</div></td>
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
         <td style="background-color:white;">
            <div align="left" style="padding:5px;font-size:85%;line-height:110%;">
               Warning message (HTML) to be shown to visitors who are not authorized to view premium contents.
               <div style="background-color:#DFD;padding-bottom:2px;margin:5px 0;border-bottom:1px dotted gray;border-top:1px dotted gray;">Paid membership example:</div><?php echo $premium_content_warning_premium_html; ?>
               <div style="background-color:#DFD;padding-bottom:2px;margin:5px 0;border-bottom:1px dotted gray;border-top:1px dotted gray;">Free membership example:</div><?php echo $premium_content_warning_free_html; ?>
               <div style="background-color:#DFD;padding-bottom:2px;margin:5px 0;border-bottom:1px dotted gray;border-top:1px dotted gray;">Delayed access example (<a target="_blank" href="http://toprate.org/tsi"><b>TSI version only</b></a>):</div><?php echo $premium_content_warning_delayed_html; ?>
            </div>
         </td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Welcome email from name:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="welcome_email_from_name" value="<?php echo $mwx_settings['welcome_email_from_name']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This will be displayed as the From: name for email sent to new customer</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Welcome email subject:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="welcome_email_subject" value="<?php echo $mwx_settings['welcome_email_subject']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Subject of email sent to new customer</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Welcome email body:</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="font-size:10px;" name="welcome_email_body" cols=90 rows=5><?php echo $mwx_settings['welcome_email_body']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Body of welcome email to be sent to new customer. Variables in curly brackets, such as: {FIRST_NAME} {LAST_NAME} and others will be substituted with their respective values from customer payment details.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Keep access to ended subscriptions?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="keep_access_for_ended_subscriptions" value="0" /><input type="checkbox" style="float:none;" value="1" name="keep_access_for_ended_subscriptions" <?php if ($mwx_settings['keep_access_for_ended_subscriptions']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: When subscription ended normally (end of term) user can still access premium subscription-based content, Disabled: when subscription ended normally - user will be denied access to premium content.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Delete emptyhanded user?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="delete_emptyhanded_user" value="0" /><input type="checkbox" style="float:none;" value="1" name="delete_emptyhanded_user" <?php if ($mwx_settings['delete_emptyhanded_user']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled (not recommended): when member does not own any products (after receiving a refund or cancelling subscription) his account will be automatically deleted from the blog.<br />Disabled (recommended): customer who no longer owns any products (after receving a refund or cancelling subscription) will be downgraded to regular subscriber but will remain a member of blog without access to any premium content.<br />Note:<br />- Because every member of your site is also automatically your affiliate - deleting him will erase all information about his referrals, payouts and balances. Use with caution!<br />- If administrator manually deleted all customer's products - customer's account will not be auto-deleted even if this setting is enabled.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Keep cancelled subscriptions active until the end of term?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="keep_cancelled_subs_active_till_eot" value="0" /><input type="checkbox" style="float:none;" value="1" name="keep_cancelled_subs_active_till_eot" <?php if ($mwx_settings['keep_cancelled_subs_active_till_eot']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: premium access will stay until the end of term.<br />Disabled: cancels access to premium content immediately.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Hide comments from non-logged on users?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="hide_comments_from_non_logged_on_users" value="0" /><input type="checkbox" style="float:none;" value="1" name="hide_comments_from_non_logged_on_users" <?php if ($mwx_settings['hide_comments_from_non_logged_on_users']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: visitors will not see comments for articles/pages until they will log in.<br />Disabled: Comments for articles/pages will always be visible</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Enable MemberWing legacy compatibility mode?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="memberwing_legacy_compatibility_mode" value="0" /><input type="checkbox" style="float:none;" value="1" name="memberwing_legacy_compatibility_mode" <?php if ($mwx_settings['memberwing_legacy_compatibility_mode']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: MemberWing-X will recognize legacy-style premium markers: <b>{+} {++} {+++} {++++}</b> used in MemberWing 2.x-4.x. <b>Note</b>: this will make it a bit slower.<br />Disabled: ignores old-style markers</div></td>
      </tr>

      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Admin acts like regular non-logged on visitor?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="admin_acts_like_regular_visitor" value="0" /><input type="checkbox" style="float:none;" value="1" name="admin_acts_like_regular_visitor" <?php if ($mwx_settings['admin_acts_like_regular_visitor']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: admin will not see premium content if it is premium. Useful for testing without need to logoff or switch browsers.<br />Disabled: all-powered admin like yourself sees everything.<br />Notes:<br />- You may add/remove/disable/enable products for members in your admin panel (Admin->Users) and see exactly what regular visitor, product owners and subscribers will see without need to logoff or changing browsers</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Show premium content warning on Home page?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="show_premium_content_warning_for_home_page" value="0" /><input type="checkbox" style="float:none;" value="1" name="show_premium_content_warning_for_home_page" <?php if ($mwx_settings['show_premium_content_warning_for_home_page']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: premium content warning message will be shown on home page if any premium content is displayed on home page.<br />Disabled: no premium content warning will be shown after free teaser on home page.<br /><b>Notes</b>:<br />- When home page renders category listing - disabling this setting will help to make layout more compatible with different themes<br />- When front page is set to display a single premium page (via Wordpress-&gt;Settings-&gt;Reading) premium content warning message will always be shown even if this setting is off</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Show premium content warning on category pages?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="show_premium_content_warning_for_category_pages" value="0" /><input type="checkbox" style="float:none;" value="1" name="show_premium_content_warning_for_category_pages" <?php if ($mwx_settings['show_premium_content_warning_for_category_pages']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: premium content warning message will be shown on category pages after excerpts.<br />Disabled: When category page with article excerpts is shown - no premium content warning text will show up after free teasers.<br /><b>Note</b>: disabling this setting will make layout more compatible with different themes.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;"><a href="http://googlewebmastercentral.blogspot.com/2008/10/first-click-free-for-web-search.html" target="_blank">Google First Click Free</a> functionality enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="first_click_free_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="first_click_free_enabled" <?php if ($mwx_settings['first_click_free_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">When enabled MemberWing-X will show full content of the first page to users coming from search engines as per <a href="http://googlewebmastercentral.blogspot.com/2008/10/first-click-free-for-web-search.html" target="_blank"><b>Google First Click Free specification</b></a>.<br /><b>Notes</b>:<br />- Enabling this feature will allow Google to index all your premium content. This will likely award you <b>higher ranking on search engines and potentially more visitors</b> but for the cost of more visibility (less protection) for premium content. Although for many membership sites benefits of higher rankings and more quality buying traffic outweight concerns of one page exposure for premium content<br />- Only first premium page will be shown to visitor and only in case if he clicked on its link directly from search engine result page. Other premium pages will still be protected by MemberWing-X and to view other pages visitor will still need to subscribe/join or pay per your requirements.<br />- First Click Free feature does not affect digital downloadable materials guarded by Digital Content Protection function of MemberWing-X.</div></td>
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
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">IP Protection enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="ip_protection_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="ip_protection_enabled" <?php if ($mwx_settings['ip_protection_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: Access to premium content will be restricted to limited number of IP addresses to prevent account credentials sharing.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">IP Protection<br />max allowed addresses</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="ip_protection_max_allowed_addresses" value="<?php echo $mwx_settings['ip_protection_max_allowed_addresses']; ?>" size="6" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Maximum number of different IP addresses user may access premium contents from.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">IP Protection<br />IP address filter mask</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <select name="ip_protection_cidr_mask" size="1">
                 <option value="32" <?php echo ($mwx_settings['ip_protection_cidr_mask']=='32')?'selected="selected"':""; ?> >NNN.NNN.NNN.NNN</option>
                 <option value="24" <?php echo ($mwx_settings['ip_protection_cidr_mask']=='24')?'selected="selected"':""; ?> >NNN.NNN.NNN.*</option>
                 <option value="16" <?php echo ($mwx_settings['ip_protection_cidr_mask']=='16')?'selected="selected"':""; ?> >NNN.NNN.*.*</option>
               </select>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">NNN.NNN.NNN.NNN - exact match between IP addresses will be considered when comparing user's current IP address with the list of IP addresses user is allowed to have access from.
         	<br />NNN.NNN.NNN.* - last octet will be ignored. Ex: if user is trying to access premium content from 123.45.6.78 and previously he accessed content from 123.45.6.99 - he will be allowed to access content. But if he is trying to access content from 123.45.1.117 - he will be disallowed.
         	<br />NNN.NNN.*.* - last two octet will be ignored. Ex: if user is trying to access premium content from 123.45.6.78 and previously he accessed content from 123.45.41.92 - he will be allowed to access content. But if he is trying to access content from 123.84.1.117 - he will be disallowed.
         </div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">IP Protection<br />Access denied message</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="font-size:10px;" name="ip_protection_access_denied_message" cols=90 rows=5><?php echo $mwx_settings['ip_protection_access_denied_message']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Message to be shown to member when he is disallowed access to premium content due to IP address access restriction
         </div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">IP Protection<br />log history size</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="ip_protection_log_history_size" value="<?php echo $mwx_settings['ip_protection_log_history_size']; ?>" size="6" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Maximum number of entries to keep within member's access log. Access log shows IP address history, datetime of logins and browser type used to access premium content.<br />
         	Access log may be accessed by administrator only within each member's profile area.</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFA;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>
<?php
}
//===========================================================================

//===========================================================================
function MWX__render_dcp_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();

   //---------------------------------------
   // Check if license is valid
      if (!MWX__License_Allowed ($mwx_settings))
         {
         $is_disabled = ' disabled="disabled" ';
         echo MWX__get_admin_license_error_message ($mwx_settings);
         }
      else
         $is_disabled = '';
   //---------------------------------------
?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Digital Content Protection with Tracefusion (advanced theft protection and tracing) Settings</div>
</div>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td style="background-color:#B5FFA8" width="15%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="45%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Physical location (directory) for your premium files:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="protected_files_physical_addr" value="<?php echo $mwx_settings['protected_files_physical_addr']; ?>" size="70" <?php echo $is_disabled; ?>/>/</div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Use FTP program to upload your premium files in this directory. You may also create subdirectories under this tree and upload your files in there.<br />MemberWing-X will try to create this directory. Make sure write access is enabled or create this directory yourself via your FTP program.<br /><b><span style="color:red;">Note:</span></b> Setting this value incorrectly will render Digital Content Protection inoperable. It is advised to leave this setting at its default value.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">WEB URL to link to above premium files:</div></td>
         <td style="background-color:#CCC;"><div align="center"><?php echo rtrim(get_bloginfo ('wpurl'), '/'); ?>/<input type="text" name="protected_files_logical_addr" value="<?php echo $mwx_settings['protected_files_logical_addr']; ?>" <?php echo $is_disabled; ?>/>/<div style="text-align:left;margin-top:10px;padding:0 10px;"><span style="font-size:85%;"><b>Note 1</b>: You may only edit part after main blog URL<br /><b>Note 2</b>: You may need to deactivate/reactive plugin and completely clear the cache of your browser to make the changes in this setting to take effect in your tests.</span></div></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Use this URL to build secure links to your premium files.<br />&nbsp;&nbsp;For example according to your current settings, this URL:<br /><?php echo '<span style="color:blue;"><b>' . rtrim(get_bloginfo ('wpurl'), '/') . '/' . $mwx_settings['protected_files_logical_addr'] . '/your-premium-doc.pdf' . '</b></span>'; ?><br />&nbsp;&nbsp;will allow premium logged on user to see this file:<br /><?php echo '<span style="color:green;"><b>' . $mwx_settings['protected_files_physical_addr'] . '/your-premium-doc.pdf' . '</b></span>'; ?><br />&nbsp;&nbsp;while non-premium user or visitor will either see "File not found" error or this file (if you uploaded it):<br /><?php echo '<span style="color:red"><b>' . $mwx_settings['protected_files_physical_addr'] . '/your-premium-doc_denied.pdf' . '</b></span>'; ?></div></td>
      </tr>



      <tr>
         <td colspan="3" style="background-color:white;">
            <div align="center" style="padding-left:5px;">
               <u>Priority order of subdirectory names (highest to lowest)</u>:<br />&nbsp;&nbsp;&nbsp;<span style="padding:3px;background-color:#FFC;"><b>Locked</b>, <b>Unrestricted</b>, /free/, <b>Individual</b>, <b>Group</b>, <b><i>unspecified</i></b></span>.<br />Higher priority overrides lower priority.
               For more help see <u>Reserved Subdirectory Names</u> section of <a href="http://www.memberwing.com/mwxm" target="_blank">MemberWing-X manual</a>.
            </div>
         </td>
      </tr>


      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;"><b>Locked Access</b> Subdirectory Names:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="locked_access_directory_names" value="<?php echo $mwx_settings['locked_access_directory_names']; ?>" size="70" maxlength="512" <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Comma-delimited list.<br />Names of subdirectories to keep files that are unavailable for purchase, invisible and unaccessable to others. Use these directories for temporary stuff, backups and other housekeeping needs.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;"><b>Unrestricted Access</b> Subdirectory Names:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="unrestricted_access_directory_names" value="<?php echo $mwx_settings['unrestricted_access_directory_names']; ?>" size="70" maxlength="512" <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Comma-delimited list.<br />Names of subdirectories used to store helper files such as images, icons, stylesheets, javascripts, etc...<br />Files located in <b>Unrestricted</b> directory <u>and it's subdirectories</u> are accessable to anyone including free visitors.<br /><b>Notes</b>:<br />- Files inside unrestricted directory trees cannot be used as a products for sale via Digital Online Store Builder. But they can be used to store icons, preview images or free snippets.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;"><b>Individual Access</b> Subdirectory Names:<br />(files for individual purchase)</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="individual_access_directory_names" value="<?php echo $mwx_settings['individual_access_directory_names']; ?>" size="70" maxlength="256" <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Comma-delimited list.<br />Names of subdirectories to store files for individual purchase. To access any file under these directories member must have purchased that file individually.<br /><b>Notes</b>:<br /> - Any directory whose name starts with underscore '_' or dot '.' is individual access directory, regardless of these settings.<br /> - It is possible to create a product to sell access to set of files defined by range of dates inside of Individual Access directory. See description of <b>fs-daterange</b> specification below.</td></div>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;"><b>Group Access</b> Subdirectory Names:<br />(group of files accessable to owners of matching products)</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="group_access_directory_names" value="<?php echo $mwx_settings['group_access_directory_names']; ?>" size="70" maxlength="512" <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Comma-delimited list.<br />Names of subdirectories to be matched against product names to give user access to files under it. For example:<br />to access this file: <b>/premium/gold/file.pdf</b> - user must own product with keyword "gold" in it's name, such as "Gold Membership"<br /><b>Notes</b>:<br /> - Directory name: <b>free</b> is reserved for files accessable for any logged on user.<br /> - If files are located under the tree that is neither Locked, nor 'free' nor Individual, nor Group, nor Unrestricted access directories - then user who owns at least one product (doesn't matter which one) will be able to access these files. Such directory is categorized as <i>Unspecified</i></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">TraceFusion Digital Content Watermarking and Tracing enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="tracefusion_tracing_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="tracefusion_tracing_enabled" <?php if ($mwx_settings['tracefusion_tracing_enabled']) echo ' checked="checked" '; ?> <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This feature allows you to pinpoint and eliminate source of your premium content leaks.<br />When premium user accesses and downloads your premium content - TraceFusion uniquely marks each premium download with digital encrypted signature containing information about identity of premium member. Having found digitally signed file illegally shared or distributed without your permission you will be able to discover the identity of the member who leaked it. Then you will be able to immediately terminate his access to your portal.<br /><a href="http://www.tracefusion.com/" target="_blank"><b>Read digital signatures and trace your digital content here</b></a><br /><b>Please note</b>:<br />- Only you will be able to decode signatures from digital materials originated from your site. MemberWing-X API key is required for that. See MemberWing-X General Settings to get API key.<br />- Text files are not watermarked even if this feature is enabled.</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" <?php echo $is_disabled; ?>/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFA;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');" <?php echo $is_disabled; ?>/>
         </div></td>
      </tr>
   </table>
</form>
<?php

   MWX__render_dcp_settings_page_html_Instructions ();
}
//===========================================================================

//===========================================================================
function MWX__render_dos_settings_page_html ()
{
   // In mwx-dos-admin.php
   MWX__render_dos_settings_page_html_2 ();
}
//===========================================================================

//===========================================================================
function MWX__render_categories_settings_page_html ()
{
   global $wpdb;
   $category_table_row_template =<<<TTT
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">{CATEGORY_NAME}</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="categories_settings[{CATEGORY_ID}][teaser_length]" value="{CATEGORY_TEASER_LENGTH}" size="10" /></div></td>
         <td style="background-color:#CCC;"><div align="center"><b>{{{</b><input type="text" name="categories_settings[{CATEGORY_ID}][premium_marker]" value="{CATEGORY_PREMIUM_MARKER}" size="60" /><b>}}}</b></div></td>
      </tr>
TTT;

   $mwx_settings = MWX__get_settings ();

   // Read list of all existing categories and generate HTML for table

   $sql_query = "
   SELECT
      $wpdb->terms.term_id,
      $wpdb->terms.name
   FROM $wpdb->terms
      JOIN $wpdb->term_taxonomy ON ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
   WHERE $wpdb->term_taxonomy.taxonomy = 'category'
   ";

   $categories_arr = $wpdb->get_results ($sql_query, ARRAY_A);

   $category_rows = "";
   if (is_array($categories_arr) && is_array(@$categories_arr[0]))
      {
      foreach ($categories_arr as $category)
         {
         $next_row = $category_table_row_template;
         $next_row = str_replace ('{CATEGORY_NAME}',           $category['name'],    $next_row);
         $next_row = str_replace ('{CATEGORY_ID}',             $category['term_id'], $next_row);
         $teaser_length  = isset($mwx_settings['categories_settings'][$category['term_id']]['teaser_length'])?$mwx_settings['categories_settings'][$category['term_id']]['teaser_length']:"";
         $premium_marker = isset($mwx_settings['categories_settings'][$category['term_id']]['premium_marker'])?$mwx_settings['categories_settings'][$category['term_id']]['premium_marker']:"";
         $next_row = str_replace ('{CATEGORY_TEASER_LENGTH}',  $teaser_length,  $next_row);
         $next_row = str_replace ('{CATEGORY_PREMIUM_MARKER}', $premium_marker, $next_row);

         $category_rows .= $next_row;
         }
      }
?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;margin:15px 0;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Category-wide premium markers</div></td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="35%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Category name</div></td>
         <td style="background-color:#B5FFA8" width="15%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Length of free teaser<br /><span style="font-size:12px;">In characters</span></div></td>
         <td style="background-color:#B5FFA8" width="50%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Premium marker<br /><span style="font-size:12px;">Will be applied automatically to every post belonging to this category. You may override this global setting by inserting your own marker into any post of your choice.<br /><span style="font-size:90%;">Hint: insert this marker <b>{{{?:0}}}</b> <u>inside the article or page</u> to make it immediately visible for everyone regardless of global category-wide setting.</span></span></div></td>
      </tr>

<?php echo $category_rows; ?>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>
</form>

<?php
}
//===========================================================================

//===========================================================================
function MWX__render_products_settings_page_html ()
{
   $mwx_settings = MWX__get_settings ();
   $is_tsi = MWX__License_TSI ($mwx_settings);
   if ($is_tsi)
      {
      $possibly_disabled = '';
      $non_tsi_warning   = '';
      }
   else
      {
      $non_tsi_warning   = '<div align="center" style="margin:8px;border:2px solid red;padding:4px;background-color:#FF5;"><b style="color:red;font-size:110%;">WARNING:</b><br /><b><u>Time-Sensitive Information settings and functions are disabled</u></b><br /><span style="font-size:110%;font-weight:bold;"><a target="_blank" href="http://toprate.org/tsi">MemberWing-X <span style="color:#DD0000;">TSI</span> license</a></span> is required for time-sensitive and time-critical functions to work.</div>';
      $possibly_disabled = ' disabled="disabled" ';
      }
?>

   <!-- Universal Integration with Paypal shopping carts and payment systems -->
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

   <table style="background-color:#555;margin:15px 0;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">
            <b><u>TSI:</u> Time-Sensitive Information settings</b><br />Products Access Delays</div>
            <?php echo $non_tsi_warning; ?>
         </td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="60%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Product keyword:access delay</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <span style="font-size:10px;line-height:12px;">Sample values were preloaded for your reference only.<br />Feel free to erase or modify them according to notes.<br /></span>
               <textarea name="products_access_delays" cols=20 rows=8 <?php echo $possibly_disabled; ?>><?php echo $mwx_settings['products_access_delays']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">
            Format: <b>keyword:NNN</b>, where:
            <br />&nbsp;&nbsp;&nbsp;<b>keyword</b> - is a name of product or keyword matching the name of product in case insensitive substring manner.
               Keyword <b>gold</b> will match "<b>Gold Membership</b>" product.
            <br />&nbsp;&nbsp;&nbsp;<b>NNN</b> - is access delay time, in formats such as: 30d, 12h or 20m. It allows you to specify delay in days, hours or minute precision.
            <br />
            <br />For example by adding this setting here:
            <br /><b>gold:20m</b> - you will force members who own "Gold Membership Subscription" product to wait 20 minutes since article publish date before being able to read full
            content of such article protected with {{{gold}}} premium marker.
            <br /><b>20m</b> defines delay to access article <u>since it has been first published</u>.
            Examples:<br />
            <br />&nbsp;&nbsp;&nbsp;<b>?:30d&nbsp;&nbsp;&nbsp;</b> - means non-logged on visitor will have to wait 30 days since published date before being able to read article protected with {{{?}}} premium marker.
            <br />&nbsp;&nbsp;&nbsp;<b>*:14d&nbsp;&nbsp;&nbsp;</b> - means any logged on visitor will wait at most 14 days before being able to access premium article protected with {{{*}}} marker.
            <br />&nbsp;&nbsp;&nbsp;<b>$:10d&nbsp;&nbsp;&nbsp;</b> - means logged on visitor that owns at least one product (any) will wait at most 10 days before being able to access premium article protected with {{{$}}} marker.
            <br />&nbsp;&nbsp;&nbsp;<b>SilverTrader:24h&nbsp;&nbsp;&nbsp;</b> - means logged on owner of "SilverTrader Access" product will wait at most 24 hours before being able to access premium article protected with {{{SilverTrader}}} marker.
            <br />&nbsp;&nbsp;&nbsp;<b>GoldMember:20m&nbsp;&nbsp;&nbsp;</b> - means means logged on owner of "GoldMember Membership" product will wait at most 20 minutes before being able to access premium article protected with {{{GoldMember}}} marker.
            <br />&nbsp;&nbsp;&nbsp;<b>Platinum:0&nbsp;&nbsp;&nbsp;</b> - means means logged on owner of "Platinum Super Member" product will have immediate access to article protected with {{{platinum}}} marker.
               <b>NOTE</b>: specifying zero delay like this 'Platinum<b>:0</b>' is not necessary and is essentially redundant. Same result is achieved just by not making any entry for 'Platinum' keyword at all.
            <br />
            <br />Notes:
            <br /> - Delay time is always calculated since artile was published very first time. Subsequent editings of article does not "reset" delay time calculations.
            <br /> - Enter each product <i>keyword:delay</i> on a new line.
            <br /> - Locally specified delay will override globally specified delay.
               For example this marker: <b>{{{?:5d}}}</b> will allow free visitor to access article only 5 days since it has been published,
               even if globally specified setting (here) is set to: <b>?:30d</b>
               <br />&nbsp;&nbsp;&nbsp;This allows webmaster to specify longer or shorter delays per article per product, overriding default settings here.
            <br /> - Specifying <b>Something:0</b> is redundant and is the same as not specifying it at all.
            <br />In this table you only need to add products (keywords) that require mandatory delays to access premium content.
            <br /> - Access will be enabled automatically once delay time is passed. This enables important SEO capabilities for your membership website.
               For example this marker {{{?}}} will automatically allow Google and other search engines to index your article once delay time is passed.
               This will help with organic ranking for your time-sensitive membership site.
         </td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>

   <table style="background-color:#555;margin:15px 0;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;"><b>Products Lifetimes</b></div></td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="60%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Product keyword:lifetime</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <span style="font-size:10px;line-height:12px;">Sample values were preloaded for your reference only.<br />Feel free to erase or modify them according to notes.<br /></span>
               <textarea name="products_lifetimes" cols=20 rows=6><?php echo $mwx_settings['products_lifetimes']; ?></textarea>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Format: <b>keyword:NNN</b>, where <b>keyword</b> is a name of product or keyword matching the name of product in case insensitive substring manner. For example keyword <b>gold</b> will match "<b>Gold Membership</b>" product.<br /><b>NNN</b> is a lifetime of product in hours or days. Example:<br />&nbsp;&nbsp;&nbsp;<b>gold:30d<br />&nbsp;&nbsp;&nbsp;Silver Membership:180d</b><br />&nbsp;&nbsp;&nbsp;<b>DailyTrial:24h</b><br />Enter each product on a new line.<br />This option lets you set lifetime for a product or group of products. After owning product for <b><i>lifetime</i></b> days - the product will be marked as expired and member automatically loses access to premium content. To regain premium access for another period of <b><i>lifetime</i></b> days member will need to purchase this product again.<br />Notes:<br /> - This option allows to override predefined "end of term" of subscription products, allowing member to have premium access beyond original subscription expiration date.<br /> - This option also lets you limit the timeframe of a single purchase memberships (which is infinite by default).<br /> - This option will override "keep access to ended subscriptions" setting (in MemberWingX General Settings screen). Subscription will be marked as "expired" after lifetime days and user will lose premium access.</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
         </div></td>
      </tr>
   </table>

</form>

<?php

}
//===========================================================================

//===========================================================================
function MWX__render_invitation_codes_settings_page_html()
{
   $mwx_settings = MWX__get_settings ();

   // Delete empty or marked-for-deletion elements.
   $invitation_codes = $mwx_settings['invitation_codes'];
   $dirty = FALSE;
   foreach ($invitation_codes as $idx=>$invitation_code)
      {
      if (!trim($invitation_code['invitation_code']) || $invitation_code['delete'])
         {
         unset ($mwx_settings['invitation_codes'][$idx]);            // Remove element from array.
         $dirty = TRUE;
         }
      }
   if ($dirty)
      {
      // Reindex array.
      $mwx_settings['invitation_codes'] = array_values ($mwx_settings['invitation_codes']);
      MWX__update_settings ($mwx_settings);
      }
?>
<div style="padding:15px;">
Invitation Codes allows you to offer trials, temporary or complimentary access to any product for new members who used these invitation codes. Invitation codes allows you to offer extra incentives for new members to try your services and make it easy to grant access to your site to Joint Venture and business partners or give gift subscriptions.
<br /><span style="border:1px solid red; padding:2px;background-color:#FFA;">To implement this functionality you need to have <a href="http://toprate.org/gf" target="_blank"><b>Gravity Forms, Developers Edition</b></a> plugin installed and activated.</span> With the help of Gravity Forms you'll be able to add special "Invitation Code"
field on your registration page. See more information at our site: <a href="http://www.memberwing.com/">www.MemberWing.com</a>

</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" width="100%" border="1">
      <tr>
         <td colspan="9" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;"><div align="center">Invitation Codes</div></td>
      </tr>
      <tr valign="top">
         <td width="20%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Invitation Code</strong><br /><span style="font-size:85%;"><br />Example:<br /><b>HAPPY_YEAR_PROMO-592764</b></span></div>
         </td>
         <td width="5%" style="background-color:#EEE;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Total Use Count</strong><br /><span style="font-size:85%;"><br />Usage stats</span></div>
         </td>
         <td width="20%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Max Use Count</strong><br /><span style="font-size:85%;"><br /><b>0</b>, <b>-1</b> or empty means unlimited use count. After max use count reached - Assigned Product will <u>not be added</u> to next new member</span></div>
         </td>
         <td width="5%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Expiration date</strong><br /><span style="font-size:85%;"><br />Expiration date of Invitation Code.<br />Beyond expiration date no product will be assigned to new user. Format:<br /><b>2011-12-31</b> or <b>2011-12-31&nbsp;23:59:59&nbsp;EST</b> <br />Leaving it empty means this invitation code <u>never expires</u>.</span></div>
         </td>
         <td width="30%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Assigned Product</strong><br /><span style="font-size:85%;"><br />This product will be automatically added to new member who used this invitation code. Example:<br /><b>Gold Membership</b><br />or<br /><b>1&nbsp;Day&nbsp;Access&nbsp;Pass&nbsp;[fs-daterange:market/alert:today:=1&nbsp;day]</b></span></div>
         </td>
         <td width="20%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Product Lifetime<br />or Expiration date</strong>
               <br />
               <span style="font-size:85%;">
                  <br />Product will automatically expire <b><u>after</u></b> this timeframe (calculated from the moment of user's registration date) or after specified date. Examples:
                  <br /><b>12 hours</b> or <b>1 day</b> or <b>21&nbsp;days</b> or
                  <b>1&nbsp;week</b> or <b>4&nbsp;weeks</b> or
                  <b>1&nbsp;month</b> or <b>6&nbsp;months</b> or
                  <b>1&nbsp;year</b> or <b>5&nbsp;years</b> or
                  <b>2011-12-31</b> or <b>2011-12-31&nbsp;23:59:59 EST</b>
                  <br />(optional)
                  <br />Note: leaving this field empty means product will <u>never</u> expire
               </span></div>
         </td>
         <td width="20%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Referred by Affiliate (User ID)</strong><br /><span style="font-size:85%;">
            <br />All purchases made by user with this invitation code will be force-assigned to this affiliate. This will override dynamic referrals for this user<br />(optional)</span></div>
         </td>
         <td width="7%" style="background-color:#B5FFA8;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Active?</strong><br /><span style="font-size:85%;"><br /><br />Inactive - means no product will be assigned to new member</span></div>
         </td>
         <td width="3%" style="background-color:red;"><div align="center" style="padding:6px 0;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;">
            <strong>Delete!</strong><br /><span style="font-size:85%;"></span></div>
         </td>
      </tr>
<?php
   $invitation_codes = $mwx_settings['invitation_codes'];
   $idx=0;
?>
<?php foreach ($invitation_codes as $idx=>$invitation_code) : ?>
      <tr>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $idx; ?>][invitation_code]"            value="<?php echo $invitation_code['invitation_code']; ?>" size="30" /></div></td>
         <td style="background-color:#EEE;"><div align="center" style="color:red;font-weight:bold;"><?php echo $invitation_code['total_use_count']?$invitation_code['total_use_count']:'0'; ?></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $idx; ?>][max_use_count]"              value="<?php echo $invitation_code['max_use_count']; ?>" size="6" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $idx; ?>][invitation_code_expiry]"     value="<?php echo $invitation_code['invitation_code_expiry']; ?>" size="20" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $idx; ?>][assigned_product]"           value="<?php echo $invitation_code['assigned_product']; ?>" size="60" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $idx; ?>][product_lifetime_or_expiry]" value="<?php echo $invitation_code['product_lifetime_or_expiry']; ?>" size="25" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $idx; ?>][referred_by_id]"             value="<?php echo $invitation_code['referred_by_id']; ?>" size="10" /></div></td>
         <td style="background-color:white;">
            <div align="center">
               <input type="hidden" name="invitation_codes[<?php echo $idx; ?>][active]" value="0" /><input type="checkbox" name="invitation_codes[<?php echo $idx; ?>][active]" style="float:none;" value="1" <?php if ($invitation_code['active']) echo 'checked="checked"'; ?> />
            </div>
         </td>
         <td style="background-color:white;">
            <div align="center">
               <input type="hidden" name="invitation_codes[<?php echo $idx; ?>][delete]" value="0" /><input type="checkbox" name="invitation_codes[<?php echo $idx; ?>][delete]" style="float:none;" value="1" />
            </div>
         </td>
<?php endforeach; ?>
      </tr>

      <tr>
         <td colspan="9" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size: 18px;margin:0 0 10px;background:#555;color:#FFF;line-height:32px;"><div align="center">Add New Invitation Code(s):</div></td>
      </tr>

<?php for ($i=$idx+1; $i<($idx+1)+4; $i++) : ?>
      <tr>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $i; ?>][invitation_code]"            value="" size="30" /></div></td>
         <td style="background-color:#999;"><div align="center"><input type="hidden" name="invitation_codes[<?php echo $i; ?>][total_use_count]" value="0" style="display:none;"/></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $i; ?>][max_use_count]"              value="" size="6" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $i; ?>][invitation_code_expiry]"     value="" size="20" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $i; ?>][assigned_product]"           value="" size="60" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $i; ?>][product_lifetime_or_expiry]" value="" size="25" /></div></td>
         <td style="background-color:white;"><div align="center"><input type="text" name="invitation_codes[<?php echo $i; ?>][referred_by_id]"             value="" size="10" /></div></td>
         <td style="background-color:white;">
            <div align="center">
               <input type="hidden" name="invitation_codes[<?php echo $i; ?>][active]" value="0" /><input type="checkbox" name="invitation_codes[<?php echo $i; ?>][active]" style="float:none;" value="1" checked="checked" />
            </div>
         </td>
         <td style="background-color:#999;"><div align="center"><input type="hidden" name="invitation_codes[<?php echo $i; ?>][delete]" value="0" style="display:none;"/></div></td>
      </tr>
<?php endfor; ?>
      <tr>
         <td colspan="9"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="paypal_integration_code_auto_insert" value="0" /><input type="checkbox" style="float:none;" value="1" name="paypal_integration_code_auto_insert" <?php if ($mwx_settings['paypal_integration_code_auto_insert']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: MemberWing-X will automatically insert above code into every Paypal button on your site. This will make every Paypal button on your site integrated with MemberWing-X and it's affiliate system<br />Disabled: You have to manually insert Paypal Button Integration Code (above) into HTML code of every Paypal button to make sure it is integrated with MemberWing-X and it's affiliate system.</div></td>
      </tr>
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:8px 0 3px;"><a href="https://developer.paypal.com/">Paypal Sandbox</a> Settings<br />Used for testing only by advanced integrators</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Paypal Sandbox enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="paypal_sandbox_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="paypal_sandbox_enabled" <?php if ($mwx_settings['paypal_sandbox_enabled']) echo ' checked="checked" '; ?> /></div></td>
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
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
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
   <br />

</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <!-- Instructions -->
      <tr>
         <td style="background-color:white;" colspan="3"><div align="left" style="padding-left:5px;font-size:85%;line-height:110%;">
         <p><b><u>Help:</u></b><br />
   Memberwing can automatically add new members to one or more autoresponders as defined below. This applies to both free and paid memberships.
   Some integration setup is required for each autoresponder service, as listed below. Either provide the details or perform setup below for the autoresponder that you will use.
   <br />You can then create as many products to autoresponder assignments as you like. You may also provide a default autoresponder (simply set the Product Keyword to <b>default</b>).
   When someone buys a membership or registers on your site, memberwing will attempt to match them to autoresponder based on the product they purchased.
   If no match is made it will use the <b>default</b>. If no match was made and no <b>default</b> exists, then the new member won't be subscribed to any autoresponder.
   Note that this subscription feature still relies on your autoresponder service settings. If a confirmation is required, the assignment will result in that confirmation message being sent.
   </p>
       </div></td>
      </tr>

      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>

      <!-- AWeber Integration -->
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://toprate.org/aweber" target="_blank">AWeber autoresponder</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;" colspan="3"><div align="left" style="padding:8px;font-size:85%;line-height:110%;">
       <span style="color: red; background-color: rgb(255, 255, 153);">Please note:</span> You must activate MemberWing parser within your Aweber mailing list configuration panel: My Lists->Email Parser-> [x] <b><i>MemberWing</i></b>. Without this step no new subscribers will be added to your Aweber list. If you need assistance regarding this - please contact Aweber helpdesk: help@aweber.com
       </div></td>
      </tr>

      <!-- Mailchimp Integration -->
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://toprate.org/mailchimp" target="_blank">Mailchimp autoresponder</a></div></td>
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
     <!--<div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your list ID number.<br />To get mailing list ID number - go to <a href="http://admin.mailchimp.com/lists/"><b>Lists</b></a>, click Settings for your list, find list ID number at the bottom of that settings page</div>-->

      <!-- 1ShoppingCart Integration -->
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://toprate.org/1sc" target="_blank">1ShoppingCart</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">1ShoppingCart merchant ID:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="1shoppingcart_merchant_id_number" value="<?php echo $mwx_settings['1shoppingcart_merchant_id_number']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your merchant ID number.</div></td>
      </tr>

      <!-- List Associations Integration -->
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Autoresponder <=> Product assignments</div></td>
      </tr>
     <style>
     #assignment {
     background-color: white;
     width: 100%;
     }
     #assignment td {
     border-bottom: 1px solid black;
     border-right: 1px solid black;
     padding: 2px;
     cell-spacing: 0px;
     }
     #assignment .header {
     font-weight: bold;
     text-align: center;
     }
     </style>
     <tr>
        <td colspan="3">
          <table id="assignment" cellspacing="0">
            <tr>
               <td class="header" valign="top">
                  <div style="font-size:120%;border-bottom:1px solid gray;">Product Keyword</div>
                  <div align="left" style="padding: 8px; font-size: 85%; line-height: 110%;font-weight:normal;">
                     Single keyword that is matched to the product name customer purchased. For example, if your membership name is:
                     <br />'<b>Novel Writer's Gold Membership</b>'
                     <br />then you would put '<b>Gold</b>' in this field.
                     <br /><u>NOTES:</u>
                     <br />&bull; You may subscribe a member to multiple lists for a given product.
                     In fact, you may even subscribe them to lists at different autoresponder services.
                     <br />&bull; Use keyword '<b>default</b>' to create "catch-all" keyword. New member will be added to default list is no other keyword matches are found.
                  </div>
               </td>
               <td class="header" valign="top">
                  <div style="font-size:120%;border-bottom:1px solid gray;">Autoresponder list name or list ID</div>
                  <div align="left" style="padding: 8px; font-size: 85%; line-height: 110%;font-weight:normal;">
                     Autoresponder list name/ID value will look different for each autoresponder service:
                     <br />
                     <br />Aweber:&nbsp;&nbsp;&nbsp;<b>my-list-name@aweber.com</b>
                     <br />MailChimp:&nbsp;&nbsp;&nbsp;<b>20ba4c89d8</b>
                     <br />1ShoppingCart:&nbsp;&nbsp;&nbsp;<b>388542</b>
                  </div>
               </td>
               <td class="header">
                  <div style="font-size:120%;1border-bottom:1px solid gray;">Autoresponder service</div>
               </td>
               <td class="header">
                  <div style="font-size:120%;1border-bottom:1px solid gray;color:red;">Delete assignment</div>
               </td>
            </tr>
            <tr>
             <td style="background-color:#CCC;"><input type="text" name="new_autoresponder_level" size="50"></td>
             <td style="background-color:#CCC;"><input type="text" name="new_autoresponder_list" size="80"></td>
             <td style="background-color:#CCC;">
               <select  name="new_autoresponder_service">
               <option value="Aweber">Aweber</option>
               <option value="MailChimp">MailChimp</option>
               <option value="1ShoppingCart">1ShoppingCart</option>
               </select>
             </td>
             <td><input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Update List Assignment" /></td>
            </tr>

               <!-- cycle through existing autoresponder associations -->
        <?php foreach ((array)@$mwx_settings['autoresponder_assignments'] as $autoresponder_assignment) : ?>
            <tr>
               <td style="background-color:#FFD;"><?php echo $autoresponder_assignment['level']; ?></td>
               <td style="background-color:#FFD;"><?php echo $autoresponder_assignment['list']; ?></td>
               <td style="background-color:#FFD;"><?php echo $autoresponder_assignment['service']; ?></td>
               <td class="header" style="background-color:#F88;"><input type="checkbox" style="float:none;" name="delete_autoresponder_assignment_<?php echo $autoresponder_assignment['key']; ?>" /></td>
            </tr>
        <?php endforeach; ?>

         </table>
       </td>
     </tr>

      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="smtp_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="smtp_enabled" <?php if ($mwx_settings['smtp_enabled']) echo ' checked="checked" '; ?> /></div></td>
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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="smtp_use_authentication" value="0" /><input type="checkbox" style="float:none;" value="1" name="smtp_use_authentication" <?php if ($mwx_settings['smtp_use_authentication']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: SMTP authentication will be used</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="mwx_affiliate_network_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="mwx_affiliate_network_enabled" <?php if ($mwx_settings['mwx_affiliate_network_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables MemberWing-X Integrated Affiliate Network functionality for digital content sales and all sales made through paypal buttons created by you.<br />Note: to track affiliate sales through your own Paypal buttons - make sure HTML code for these buttons includes proper Paypal IPN code (see MemberWing-X Paypal settings->'Paypal IPN code') and they are included in your pages in non-encrypted plaintext format.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">First affiliate wins?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_first_affiliate_wins" value="0" /><input type="checkbox" style="float:none;" value="1" name="aff_first_affiliate_wins" <?php if ($mwx_settings['aff_first_affiliate_wins']) echo ' checked="checked" '; ?> /></div></td>
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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_promotion_to_zero_min_payout" value="0" /><input type="checkbox" style="float:none;" value="1" name="aff_promotion_to_zero_min_payout" <?php if ($mwx_settings['aff_promotion_to_zero_min_payout']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Disabled: after payout is made - affiliate will need to accumulate minimal payout threshold again to be paid.<br />Enabled: once affiliate reaches his payout threshold first time and gets paid - his personal payout threshold will automatically be set to zero (achieved min payout immunity). He won't need to accumulate threshold ever again to be paid. Payments will become instant for him (unless manual payouts or manual sale approvals are enabled).</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Approve each affiliate sale manually?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_manual_aff_sale_approval" value="0" /><input type="checkbox" style="float:none;" value="1" name="aff_manual_aff_sale_approval" <?php if ($mwx_settings['aff_manual_aff_sale_approval']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: each sale made by each affiliate must be manually approved by webmaster. Before sale is approved - it won't count toward payout.<br />Disabled: each successful affiliate sale is automatically approved.<br />Notes:<br />- Manual or Auto - Payout still need to reach min threshold to be paid out.<br />- This settings overrides '<i>Auto-approve affiliate sale in days</i>'.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Manual payouts to affiliates?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_manual_payouts" value="0" /><input type="checkbox" style="float:none;" value="1" name="aff_manual_payouts" <?php if ($mwx_settings['aff_manual_payouts']) echo ' checked="checked" '; ?> /></div></td>
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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="aff_auto_approve_affiliates" value="0" /><input type="checkbox" style="float:none;" value="1" name="aff_auto_approve_affiliates" <?php if ($mwx_settings['aff_auto_approve_affiliates']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enabled: each affiliate is auto-approved<br />Disabled: webmaster needs to manually approve every affiliate.<br />Note: if auto approve is ON and new sale is referred via link like this:<br /><?php echo get_bloginfo ('wpurl') . "?" . $mwx_settings['aff_affiliate_id_url_prefix'] . "=<b>john@smith.com</b>"; ?><br /> - then <b>john@smith.com</b> will be automatically added as a blog member during sale processing.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Affiliate ID prefix for links:</div></td>
         <td style="background-color:#CCC;"><div align="center">&nbsp;&nbsp;<input type="text" name="aff_affiliate_id_url_prefix" value="<?php echo $mwx_settings['aff_affiliate_id_url_prefix']; ?>" size="10" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Used by affiliates to build links to your site, such as:<br /><?php echo get_bloginfo ('wpurl') . "?" . "<b>" . $mwx_settings['aff_affiliate_id_url_prefix'] . "</b>" . "=john@smith.com"; ?> or:<br /><?php echo get_bloginfo ('wpurl') . "?" . "<b>" . $mwx_settings['aff_affiliate_id_url_prefix'] . "</b>" . "=123"; ?><br />Notes:<br />- Affiliate ID could be found under each affiliate User profile<br />- If email address is used to build referral link - it must be verified paypal email address. Make sure to inform your affiliates about that.<br />- '<b>aff</b>' can still be used by affiliates even if you changed it here</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Enable affiliate tracking for <a href="http://toprate.org/ecwid" target="_blank">ECWID shopping cart</a> service?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="ecwid_affiliate_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="ecwid_affiliate_integration_enabled" <?php if ($mwx_settings['ecwid_affiliate_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">When enabled - affiliate ID information will be automatically sent to ECWID Sales/Orders data. This will allow you to learn which affiliate referred a sale for you.<br />To view affiliate information for sales in ECWID shopping cart, please <a href="https://my.ecwid.com/cp/?" target="_blank">login into your ECWID admin</a> and go to: Sales-&gt;Orders, and click on order number. Affiliate referrer ID will be shown in the upper right corner of screen.<br />Notes:<br /> - If affiliate tracking was not enabled at the time of order - no affiliate ID information will be shown.<br /> - If you have any caching plugins enabled - IP address of customer may not be correct.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Allow detailed affiliate tracking for <a href="http://toprate.org/ecwid" target="_blank">ECWID shopping cart</a> service?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="ecwid_affiliate_tracking_detailed" value="0" /><input type="checkbox" style="float:none;" value="1" name="ecwid_affiliate_tracking_detailed" <?php if ($mwx_settings['ecwid_affiliate_tracking_detailed']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">When enabled - affiliate ID and customer's IP adress will be included in affiliate tracking data sent to ECWID. Example:<br />&nbsp;&nbsp;&nbsp;Affiliate: <b>aff_id:174,aff_name:johnsmith,cust_ip:98.24.177.152</b><br />If disabled - only affiliate ID will be sent:<br />&nbsp;&nbsp;&nbsp;Affiliate: <b>174</b><br />Please note that if you have any caching plugins enabled - IP address of customer may not be correct.</div></td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
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
      $aff_info  = MWX__get_usermeta_array ($affiliate_id, 'mwx_aff_info');
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
      if ($aff_payout_due > 0 && !MWX__License_Allowed ($mwx_settings))   // MWX Aff Network fee is 0 for Premium license owners
         $aff_network_fee = max (round ($aff_payout_due / 10, 2), 0.20);
      else
         $aff_network_fee = 0;
      // NOTE: Forcing Aff Network fee to be zero for everyone.
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
function MWX__render_user_management_page_html ()
{
   global $wpdb;
   $mwx_settings = MWX__get_settings ();

   global $wp_roles;
   $current_user = wp_get_current_user();

   // WP_User_Search was deprecated in WP 3.1 - use WP_User_Query instead.
   $search_term    = isset($_GET['query'])     ? trim((string)$_GET['query'])    : '';
   $users_page     = isset($_GET['userspage']) ? max(1, (int)$_GET['userspage']) : 1;
   $role_filter    = isset($_GET['role'])      ? (string)$_GET['role']           : '';
   $users_per_page = 50;

   $wpq_args = array (
      'number'      => $users_per_page,
      'paged'       => $users_page,
      'count_total' => true,
      'fields'      => 'ID',
      'orderby'     => 'ID',
      'order'       => 'ASC',
      );
   if ($search_term !== '')
      $wpq_args['search'] = '*' . $search_term . '*';
   if ($role_filter !== '')
      $wpq_args['role'] = $role_filter;

   $wps = new WP_User_Query ($wpq_args);
   $total_users = (int)$wps->get_total();
   $total_pages = (int)ceil($total_users / max(1, $users_per_page));

   $paging_text = paginate_links(array(
      'total' => max(1, $total_pages),
     'current' => $users_page,
     'base' => 'admin.php?page=memberwing-x-settings-user-management&%_%',
     'format' => 'userspage=%#%',
     'add_args' => array()
   ));
   if($paging_text) {
      $paging_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
         number_format_i18n( ( $users_page - 1 ) * $users_per_page + 1 ),
       number_format_i18n( min( $users_page * $users_per_page, $total_users ) ),
       number_format_i18n( $total_users ),
       $paging_text
      );
   }

   // Get all users
   $all_users_ids = $wpdb->get_col("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY ID ASC");
   $all_users_ids = implode (", ", $all_users_ids);


?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">User Management</div>
</div>

   <div class="wrap">
      <form class="search-form" action="" method="get">
         <p class="search-box">
            <label class="hidden" for="user-search-input">Search Users:</label>
            <input type="text" class="search-input" id="user-search-input" name="query" value="" />
            <input type="hidden" id="page" name="page" value="<?php echo esc_attr((string)@$_REQUEST['page']); ?>" />
            <input type="submit" value="Search Users" class="button" />
         </p>
      </form>
      <form id="posts-filter" action="" method="get">
         <div class="tablenav">
            <?php if($total_pages > 1) { ?>
               <div class="tablenav-pages"><?php echo $paging_text; ?></div>
            <?php } ?>
            <br class="clear" />
         </div>
         <table class="widefat fixed" cellspacing="0">
            <thead>
            <tr class="thead">
               <th scope="col" id="username" class="manage-column column-username" style="">Username</th>
               <th scope="col" id="name" class="manage-column column-name" style="">Name</th>
               <th scope="col" id="email" class="manage-column column-email" style="">E-mail</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="thead">
               <th scope="col"  class="manage-column column-username" style="">Username</th>
               <th scope="col"  class="manage-column column-name" style="">Name</th>
               <th scope="col"  class="manage-column column-email" style="">E-mail</th>
            </tr>
            </tfoot>
            <tbody id="users" class="list:user user-list">

<?php
   //
   $c = 0;
   //foreach($m as $u) {
   foreach($wps->get_results() as $u) {
      $u = new WP_User($u);
      $r = $u->roles;
      $r = array_shift($r);
      if(!empty($_REQUEST['role']) and $_REQUEST['role'] != $r) {
         continue;
      }
      $d = is_float($c/2) ? '' : ' class="alternate"';
      $nu = $current_user;
      $e = $u->ID == $nu->ID ? 'profile.php' : 'user-edit.php?user_id='.$u->ID.'&#038;wp_http_referer='.wp_get_referer();
?>
      <tr id='user-<?php echo $u->ID;?>'<?php echo $d;?>>
         <td class="username column-username">
            <?php echo get_avatar($u->ID,32);?>
            <strong>
               <a href="admin.php?page=memberwing-x-settings-user-profile&userid=<?php echo $u->ID;?>&action=profile"><?php echo $u->user_nicename;?></a>
            </strong><br />
            <div class="row-actions">
               <span class='edit'><a href="admin.php?page=memberwing-x-settings-user-profile&userid=<?php echo $u->ID;?>&action=profile">Edit Profile</a></span>
            </div>
         </td>
         <td class="name column-name"><?php echo $u->first_name.' '.$u->last_name;?></td>
         <td class="email column-email"><a href='mailto:<?php echo $u->user_email;?>' title='e-mail: <?php echo $u->user_email;?>'><?php echo $u->user_email;?></a></td>
      </tr>
<?php
      $c++;
   }
?>
            </tbody>
         </table>
      </form>
   </div>
<?php

}
//===========================================================================

//===========================================================================
function MWX__render_user_profile_page_html () {

   global $wpdb;
   $mwx_settings = MWX__get_settings ();

   MWX__update_user_profile();

?>
<form method="post">
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">User Profile</div>
</div>
<a href="admin.php?page=memberwing-x-settings-user-management">&lt;&lt; &nbsp; Return to Member Listing</a>
<?php
   MWX__show_user_profile(get_userdata((int)@$_GET['userid']));
?>
<p class="submit">
   <input type="hidden" name="action" value="update" />
   <input type="hidden" name="user_id" id="user_id" value="<?php echo (int)@$_GET['userid']; ?>" />
   <input type="submit" class="button-primary" value="Update User" name="submit" />
</p>
</form>
<?php
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
               <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" />&nbsp;&nbsp;&nbsp;&nbsp;
               <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
               <input type="submit" style="border:3px solid red;background-color:#FFC;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');"/>
            </div>
         </td>
      </tr>
SSS;

   //---------------------------------------
   // Check is Authorize.net postback script is present
   $authnet_postback_script = MWX__convert_extension_url_to_filename ($mwx_settings['authnet_post_url']);
   if (!file_exists($authnet_postback_script))
      {
      $no_authnet_postback_script_warning_message =<<<SSS
      <tr>
         <td colspan="3" style="background-color:#FF0;">
            <div align="center" style="padding:5px;"><span style="color:red;font-weight:bold;font-size:120%;">WARNING:</span> Authorize.net Postback script extension does not exists! Integration will not work.<br />Please <a href="http://www.memberwing.com/contact" target="_blank"><b>contact us</b></a> for details on how to obtain this script.</div>
         </td>
      </tr>
SSS;
      }
   else
      $no_authnet_postback_script_warning_message = '';
   //---------------------------------------

   //---------------------------------------
   // Check is infusionsoft postback script is present
   $infusionsoft_postback_script = MWX__convert_extension_url_to_filename ($mwx_settings['infusionsoft_post_url']);
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
   //---------------------------------------

   //---------------------------------------
   // Check is NMI Payments script is present
   $nmi_payments_script = MWX__convert_extension_url_to_filename ($mwx_settings['nmi_finish_url']);
   if (!file_exists($nmi_payments_script))
      {
      $no_nmi_payments_script_warning_message =<<<SSS
      <tr>
         <td colspan="3" style="background-color:#FF0;">
            <div align="center" style="padding:5px;"><span style="color:red;font-weight:bold;font-size:120%;">WARNING:</span> NMI Payments extension script does not exists! Integration will not work.<br />Please <a href="http://www.memberwing.com/contact" target="_blank"><b>contact us</b></a> for details on how to obtain this script.</div></td>
      </tr>
SSS;
      }
   else
      $no_nmi_payments_script_warning_message = '';
   //---------------------------------------

   //---------------------------------------
   // Check is PNP Password directory is present
   if (!@is_dir($mwx_settings['pnp_password_file_dir']))
      {
      $no_pnp_password_file_dir_message =<<<SSS
      <tr>
         <td colspan="3" style="background-color:#FF0;">
            <div align="center" style="padding:5px;">
               <span style="color:red;font-weight:bold;font-size:120%;">WARNING:</span> PlugNPay passwords file upload location (directory) is not valid.<br />Please set it to valid value and <span style="color:red;font-weight:bold;">make sure it is protected</span> with proper .htaccess file!
               <br />Note: your PlugNPay integration will function even without password upload directory setting, but only for new member account creation. No cancellations will be processed.
            </div>
         </td>
      </tr>
SSS;
      }
   else if (!file_exists($mwx_settings['pnp_password_file_dir'] . '/.htaccess'))
      {
      $no_pnp_password_file_dir_message =<<<SSS
      <tr>
         <td colspan="3" style="background-color:#FF0;">
            <div align="center" style="padding:5px;">
               <span style="color:red;font-weight:bold;font-size:120%;">WARNING:</span> No .htaccess file found in PlugNPay password upload directory.
               <br />Make sure your password upload directory <span style="color:red;font-weight:bold;">is protected against unauthorized access!</span>
            </div>
         </td>
      </tr>
SSS;
      }
   else
      $no_pnp_password_file_dir_message = '';
   //---------------------------------------
?>

<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Integration with other systems, services and software</div>
</div>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

   <!-- Universal Integration with Paypal shopping carts and payment systems -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Universal Integration with third party Paypal shopping carts and Paypal payment systems</div></td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Universal Paypal integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="universal_paypal_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="universal_paypal_integration_enabled" <?php if ($mwx_settings['universal_paypal_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with third party paypal shopping carts and systems that are able to re-POST paypal payment notifications.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Universal Paypal Postback URL</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="universal_paypal_postback_url" value="<?php echo $mwx_settings['universal_paypal_postback_url']; ?>" size="80" readonly="readonly" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL to receive POST copy of Paypal payment events.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Universal Paypal Include File<br />(to be used by <a href="http://www.memberwing.com/goto/rapidactionprofits/" target="_blank"><b>RAP</b></a> script and others)</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="universal_paypal_include_file" value="<?php echo $mwx_settings['universal_paypal_include_file']; ?>" size="80" readonly="readonly" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This file may be directly included by other scripts to let MemberWing-X postprocess Paypal payment and include new member.<br /><a href="http://www.memberwing.com/goto/rapidactionprofits/" target="_blank"><b>Rapid Action Profits (RAP)</b></a> script owners: copy/paste this value into your RAP admin panel->Addons->MemberWing Addon</div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <hr />

   <!-- Authorize.net Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://toprate.org/auth-net" target="_blank"><b>Authorize.net</b></a> on-site credit card billing for both single and recurring payments.<br /><b>Note: You must install this <a href="http://toprate.org/auth-net" target="_blank">Authorize.net plugin</a> first for this integration to work.</b></div></td>
      </tr>
      <?php echo $no_authnet_postback_script_warning_message; ?>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Authorize.net Postback integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="authnet_postback_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="authnet_postback_integration_enabled" <?php if ($mwx_settings['authnet_postback_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.memberwing.com/get/authorize.net/" target="_blank">Authorize.net</a> system. If disabled - postbacks to this script will be ignored.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Authorize.net Post/Callback URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="authnet_post_url" value="<?php echo $mwx_settings['authnet_post_url']; ?>" size="100" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of script to be called by Authorize.net.</div></td>
      </tr>

      <tr>
         <td colspan="3">
            <div align="center" style="padding:6px;background-color:#FFD;">
                  <u>Authorize.net integration information:</u>
               <div align="left" style="font-size:85%;line-height:110%;">
               Please note, that updated <a href="http://toprate.org/auth-net/" target="_blank">Authorize.net settings and integration instructions are available here.</a>
               When customer buys product or fills in the form:
                  <ol style="font-size:95%;margin-top:5px;">
                     <li>Notification will be sent to this script</li>
                     <li>New user account will be created</li>
                     <li>Email will be dispatched to new customer as well as to website administrator with new customer information and login credentials</li>
                     <li>Tip: you may edit the contents of email sent to customer in MemberWingX-&gt;General Settings-&gt;Welcome email</li>
                  </ol>
               </div>
            </div>
         </td>
      </tr>


   <?php echo $save_settings; ?>
   </table>
   <hr />

   <!-- E-junkie Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://toprate.org/ejunkie" target="_blank">E-Junkie.com</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;" width="30%"><div align="left" style="padding-left:5px;">E-junkie Payment Variable Information URL:</div></td>
         <td style="background-color:#CCC;"  width="40%"><div align="center"><input type="text" name="ejunkie_ipn_url" value="<?php echo $mwx_settings['ejunkie_ipn_url']; ?>" size="80" onclick="this.select();" /></div></td>
         <td style="background-color:white;" width="30%"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Copy/paste this value into Payment Variable Information URL field (e-junkie product editing)</div></td>
      </tr>
      <tr>
         <td colspan="3">
            <div align="center" style="padding:6px;background-color:#FFD;">
               E-junkie integration instructions. For more information see <a href="http://www.memberwing.com/mwxm/" target="_blank">MemberWing-X manual</a> and <a href="http://www.e-junkie.com/ej/help.integration.htm" target="_blank">instructions on e-junkie.com website</a>.
                  <br /><u>To integrate your product at e-junkie system with MemberWing-X please follow these steps:</u>
               <div align="left" style="font-size:85%;line-height:110%;">
                  <ol style="font-size:95%;margin-top:5px;">
                     <li>Check: <b>[x] Send transaction data to a URL</b> while adding or editing your product.</li>
                     <li>Press <b>[Submit]</b> button if you are adding the product, or press <b>[Next]</b> button if you are editing the product.</li>
                     <li>In the <b>Payment Variable Information URL</b> field, enter above value (E-junkie Payment Variable Information URL).</li>
                     <li>Press <b>[Next]</b> button till you can <b>[Submit]</b> to reach the button code screen.</li>
                     <li>Use the Buy NOW or E-junkie Cart button codes from this screen to start selling your product (if you had already copy-pasted the button code for this product and made no other changes to the product, your existing code will continue to work fine).</li>
                     <li>When customer will purchase your product, MemberWing-X will be notified, new member account will be created and new member will be able to login and access premium content according to your rules.</li>
                  </ol>
               </div>
            </div>
         </td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <hr />

   <!-- ClickBank Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with ClickBank</div></td>
      </tr>
      <tr>
         <td style="background-color:white;" width="30%"><div align="left" style="padding-left:5px;">ClickBank Instant Notification URL:</div></td>
         <td style="background-color:#CCC;"  width="40%"><div align="center"><input type="text" name="clickbank_ipn_url" value="<?php echo $mwx_settings['clickbank_ipn_url']; ?>" size="80" onclick="this.select();" /></div></td>
         <td style="background-color:white;" width="30%"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Copy/paste this value into Clickbank-&gt;Account settings-&gt;My site-&gt;Advanced tools-&gt;Instant Notification URL</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">ClickBank Secret Key:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="clickbank_secret_key" value="<?php echo $mwx_settings['clickbank_secret_key']; ?>" size="35" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Your Clickbank Secret Key (used in Clickbank-&gt;Account settings--&gt;My site-&gt;Advanced tools-&gt;Secret Key)</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">ClickBank Product Keyword:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="clickbank_product_keyword" value="<?php echo $mwx_settings['clickbank_product_keyword']; ?>" size="35" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Only products that contain this keyword in their names will be processed by MemberWing. Others will be ignored. Example:<br />&nbsp;&nbsp;&nbsp;<i>gold</i><br />Above keyword will match products named: "Gold membership", "Goldfinger movie review" but not "Silver Membership". Matching is case-insensitive. If this is empty - all product sales refered from Clickbank will be processed as membership products</div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <hr />

   <!-- 2Checkout Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.2checkout.com/" target="_blank">2Checkout.com</a></div></td>
      </tr>
      <tr>
         <td style="background-color:white;" width="30%"><div align="left" style="padding-left:5px;">2Checkout Instant Notification URL:</div></td>
         <td style="background-color:#CCC;"  width="40%"><div align="center"><input type="text" name="2co_ipn_url" value="<?php echo $mwx_settings['2co_ipn_url']; ?>" size="80" onclick="this.select();" /></div></td>
         <td style="background-color:white;" width="30%"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Copy/paste this value into Global Settings-&gt;Global URL (see instructions)</div></td>
      </tr>
      <tr>
         <td colspan="3">
            <div align="center" style="padding:6px;background-color:#FFD;">
               2Checkout integration instructions.
               <div align="left" style="font-size:85%;line-height:110%;">
                  <ol style="font-size:95%;margin-top:5px;">
                     <li>Login to your 2Checkout admin panel.</li>
                     <li>Paste "2Checkout Instant Notification URL" (above) into 2Checkout admin panel at: Account-&gt;Notifications-&gt;Global Settings-&gt;Global URL.</li>
                     <li>Press [Apply] button. </li>
                     <li>Check "Enable all notifications".</li>
                     <li>Press [Apply] button. </li>
                     <li>Scroll to the end of page and press [Save Settings] button.</li>
                  </ol>
               </div>
            </div>
         </td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <hr />

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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="idevaffiliate_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="idevaffiliate_integration_enabled" <?php if ($mwx_settings['idevaffiliate_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.memberwing.com/get/idev" target="_blank">iDevAffiliate</a> affiliate tracking software system</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">iDevAffiliate installation location/URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="idevaffiliate_install_dirname" value="<?php echo $mwx_settings['idevaffiliate_install_dirname']; ?>" size="80" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL Location of iDevAffiliate script. Format:<br /><i>http://www.YOUR-SITE-NAME.com/idevaffiliate</i></div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <hr />

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
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="infusionsoft_postback_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="infusionsoft_postback_integration_enabled" <?php if ($mwx_settings['infusionsoft_postback_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.memberwing.com/get/infusionsoft/" target="_blank">InfusionSoft</a> system. If disabled - postbacks to this script will be ignored.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">InfusionSoft Post URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="infusionsoft_post_url" value="<?php echo $mwx_settings['infusionsoft_post_url']; ?>" size="100" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of script to be called by InfusionSoft.</div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <div style="font-size:90%;line-height:110%;"><span style="font-size:120%;"><b><u>InfusionSoft integration instructions</u></b>:</span><br /><br /><?php echo $infusionsoft_instructions; ?></div>
   <hr />

   <!-- NMI Payments Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.nmi.com/" target="_blank"><b>NMI Payments system</b></a></div></td>
      </tr>
      <?php echo $no_nmi_payments_script_warning_message; ?>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
<?php
$nmi_instructions =<<<SSS
<div>Please follow these <a href="http://www.memberwing.com/memberwing-plugin/integration-instructions-with-nmi-payments/" target="_blank">NMI Integration Instructions</a></div>
SSS;
?>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">NMI Payments integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="nmi_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="nmi_integration_enabled" <?php if ($mwx_settings['nmi_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.nmi.com/" target="_blank">NMI Payments</a> system.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">NMI Finish URL (return_link):</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="nmi_finish_url" value="<?php echo $mwx_settings['nmi_finish_url']; ?>" size="100" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of script to be placed into 'Finish URL' field at 'Button Generator' screen of NMI admin panel.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Thank you page URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="nmi_thank_you_page_url" value="<?php echo $mwx_settings['nmi_thank_you_page_url']; ?>" size="100" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of Thank you page. This is the page where customer will be redirected after successful payment.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">NMI Security Key ID:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="nmi_security_key_id" value="<?php echo $mwx_settings['nmi_security_key_id']; ?>" size="25" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Security Key ID (7-digit number) found in NMI admin panel at:<br />Home-&gt;Options-&gt;Security Keys-&gt;Key ID</div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <div style="font-size:90%;line-height:110%;"><span style="font-size:120%;"><b><u>NMI Payments integration instructions</u></b>:</span><br /><br /><?php echo $nmi_instructions; ?></div>


   <hr />

   <!-- PlugNPay Integration -->
   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.plugnpay.com" target="_blank"><b>PlugNPay Payments</b></a></div></td>
      </tr>
      <?php echo $no_pnp_password_file_dir_message; ?>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
<?php
$pnp_instructions =<<<SSS
<ol>
   <li>
      Set PlugNPay password upload directory to valid value.
   </li>
   <li>
      Create FTP access account for this directory. Make sure these credentials are activated at your PlugNPay account settings. Contact PlugNPay support for more info.
   </li>
   <li>
      Make sure password upload directory is properly protected against unauthorized access by .htaccess file. Please contact PlugNPay support for instructions.
   </li>
   <li>
      Make sure Payments Notification URL is properly set at your PlugNPay account settings. This URL is listed in above table. Contact PlugNPay for instructions on how to set it at your PNP account.
   </li>
   <li>
      Please contact PlugNPay support for more detailed instructions on how to configure your PlugNPay account settings. Make sure:
      <br />- Passwords are set to support 32 characters length passwords.
      <br />- Password file upload directory FTP access credentials are correctly configured at your PnP account.
      <br />- PnP is configured to send 'email', 'username' and 'password' fields to Payment Notification URL with every purchase and payment notification.
      <br />- PnP is configured to include 'email', 'username' and 'password' fields to <b><u>passwords file</u></b> (that will be uploaded to Password file upload directory on a regular basis).
   </li>
   <li>
      Every time payment/subscription event happens - MemberWing-X will log information into __log.php file located inside of MemberWing-X plugin directory.
      <br />Check this file on a regular basis to resolve issues and make sure you are not missing any warnings.
   </li>
   <li>
      Note: if you want to manually add premium member:
      <br />- Navigate to Users-&gt;username
      <br />- Scroll down to Products table
      <br />- Manually add product named: "Premium Membership (manual)"
      <br />- [Save]
   </li>
</ol>
SSS;

?>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">PlugNPay Payments integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="pnp_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="pnp_integration_enabled" <?php if ($mwx_settings['pnp_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with PlugNPay payments.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Disable ALL outgoing emails from MemberWing?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="mwx_disable_all_emails" value="0" /><input type="checkbox" style="float:none;" value="1" name="mwx_disable_all_emails" <?php if ($mwx_settings['mwx_disable_all_emails']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Checking this box will completely disable all outgoing emails from MemberWing.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">PlugNPay Payments Notification URL:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input readonly="readonly" onclick="this.select();" type="text" name="pnp_notification_url" value="<?php echo $mwx_settings['pnp_notification_url']; ?>" size="100" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of script to receive notifications about subscriptions and payments.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">PlugNPay passwords upload directory:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="pnp_password_file_dir" value="<?php echo $mwx_settings['pnp_password_file_dir']; ?>" size="100" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Physical location (directory name) where passwords file will be uploaded by PlugNPay system.<br />Make sure separate FTP account is created to access this directory and it's credentials is entered for your PlugNPay account. Please contact PlugNPay support for help.</div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <div style="font-size:90%;line-height:110%;"><span style="font-size:120%;"><b><u>PlugNPay integration instructions</u></b>:</span><br /><br /><?php echo $pnp_instructions; ?></div>
   <hr />


   <?php
   if (file_exists(dirname(__FILE__) . '/mwx-notify-pay4me.php'))
	  {
   ?>

   <!-- Pay4Me Integration -->
   <?php
	global $_mwx_form_template;

	// Replace {{{merchant_service_id}}} with $mwx_settings['pay4me_merchant_service_id'] in FORM code
	$mwx_form_template = str_replace ('{{{merchant_service_id}}}', $mwx_settings['pay4me_merchant_service_id'], $_mwx_form_template);
   ?>

   <table style="background-color:#555;margin-bottom:15px;" border="1" width="100%">
      <tr>
         <td colspan="3" style="background-color:#FDD;"><div align="center" style="padding:5px;font-size:120%;">Integration with <a href="http://www.pay4me.com/" target="_blank"><b>Pay4Me</b></a> Payments System</div></td>
      </tr>
      <tr>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="40%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Pay4me integration enabled?</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="hidden" name="pay4me_integration_enabled" value="0" /><input type="checkbox" style="float:none;" value="1" name="pay4me_integration_enabled" <?php if ($mwx_settings['pay4me_integration_enabled']) echo ' checked="checked" '; ?> /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Enables integration with <a href="http://www.pay4me.com/" target="_blank">Pay4me</a> system.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Current deployment scenario:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <select name="pay4me_deployment" size="1">
                 <option value="testing"        <?php echo ($mwx_settings['pay4me_deployment']=='testing')    ? 'selected="selected"':""; ?> >Testing</option>
                 <option value="production"     <?php echo ($mwx_settings['pay4me_deployment']=='production') ? 'selected="selected"':""; ?> >Production</option>
               </select>
            </div>
         </td>
         <td style="background-color:white;">
         	<div align="left" style="padding:5px;font-size:85%;line-height:110%;">
				Select your current deployment scenario
         	</div>
         </td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Pay4me Response URL (Payment notification URL):</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="pay4me_response_url" value="<?php echo $mwx_settings['pay4me_response_url']; ?>" size="100" onclick="this.select();" readonly="readonly" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of script to be placed into 'Finish URL' field at 'Button Generator' screen of NMI admin panel.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Pay4me Merchant Code:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="pay4me_merchant_code" value="<?php echo $mwx_settings['pay4me_merchant_code']; ?>" size="20" /></div></td>
         <td style="background-color:white;">
         	<div align="left" style="padding:5px;font-size:85%;line-height:110%;">
				A unique, numeric code assigned to the Merchant by Pay4me at the time of configuration. This code is used in the URL for placing the Order payment request at Pay4me.
         	</div>
         </td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Pay4me Merchant Key:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="pay4me_merchant_key" value="<?php echo $mwx_settings['pay4me_merchant_key']; ?>" size="20" /></div></td>
         <td style="background-color:white;">
         	<div align="left" style="padding:5px;font-size:85%;line-height:110%;">
				The Merchant Key is a unique code that helps secure Merchants communications with Pay4me.
				Both Merchant and Pay4me use this key to authenticate and verify the integrity of any messages that are exchanged.
				Merchants are suggested to never share their Merchant Key with anyone.
				Pay4me uses the Merchant Key to authenticate Merchants API requests, and no Pay4me representative will ever ask any Merchant for this Key.
         	</div>
         </td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Pay4me Merchant Service ID:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="pay4me_merchant_service_id" value="<?php echo $mwx_settings['pay4me_merchant_service_id']; ?>" size="20" /></div></td>
         <td style="background-color:white;">
         	<div align="left" style="padding:5px;font-size:85%;line-height:110%;">
			This identifies the merchant service to which the item belongs. During the merchant setup, Merchant can register for various services.
			Each service is assigned a unique id by Pay4me which is identified in this tag.
			This value is passed to "HTML buy form template". You may change this value in template manually when pasting it in your buy page.
         	</div>
         </td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">HTML buy form template</div></td>
         <td style="background-color:#CCC;">
         	<div align="center">
         		<textarea style="overflow:auto;overflow-x:scroll;font-size:10px;border:3px solid #39F;background-color:#FFC;" cols=120 rows=4 readonly="readonly" onclick="this.select();"><?php echo $mwx_form_template; ?>
         		</textarea>
         	</div>
         </td>
         <td style="background-color:white;">
         	<div align="left" style="padding:5px;font-size:85%;line-height:110%;">
         		This is HTML template to make your buy/purchase form.
         		<br />Copy this code, paste it into your buy/subscribe page and edit it to your liking.
         		<br />- Make sure that value for <b>merchant_service_id</b> is properly set within this tag: 'name="merchant_service_id" value="..."'
				<br />- Replace values for item price, item name and item description to the proper ones.
         	</div></td>
      </tr>
   <?php echo $save_settings; ?>
   </table>
   <?php  } /* if (file_exists(dirname(__FILE__) . '/mwx-notify-pay4me.php')) */ ?>


</form>
   <hr />

<?php
}
//===========================================================================

//===========================================================================
function MWX__render_dcp_settings_page_html_Instructions ()
{
?>
<style type="text/css">
<!--
.style9 {
   font-family: Arial, Helvetica, sans-serif;
   font-size: 11px;
}
.style12 {
   font-family: Arial, Helvetica, sans-serif;
   font-size: medium;
}
.style13 {color: #0000FF}
.style14 {color: #FF0000}
.style15 {color: #00CC00}
.style17 {color: #00CC00; font-weight: bold; }
.style18 {color: #CC00FF}
.style19 {font-size: small}
.style21 {color: #6699CC}
.style23 {color: #6699CC; font-weight: bold; }
.style24 {font-size: 11px;}
-->
</style>


<div align="center" style="border:1px solid gray;background-color:#FFD;margin:10px 0;">
   It is possible to create a product with a specially encoded name to sell access to a set of files defined by the range of dates inside one of Individual Access directories.
   <br />For example creating product with the name like that: <b>2 Days Access Pass [fs-daterange:downloads/markets/alerts/stocks:today:=2 days]</b>
   <br />will allow member to access set of files in directory /PREMIUM_FILES/downloads/markets/alerts/stocks/* that were released today (purchase date) and tomorrow.
   <br />See more details and explanations in the table below.
</div>

<div align="center">
  <table style="margin:10px 0;border:1px solid gray;">
    <tr>
      <td width="120" style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;"><b>Product name</b></td>
      <td><div style="background-color:#FFC;padding:3px;" align="center"><span class="style12">3 Days Access Pass [fs-daterange:<span class="style13">downloads/racing</span>:<span class="style15">today</span>:<span class="style18">+2 days</span><span class="style14">:s</span>]</span></div></td>
    </tr>
    <tr>
      <td style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;">Description</td>
      <td style="border-bottom:2px solid gray;"><span class="style9" style="line-height:12px;">Gives access to total 3 days worth of files uploaded into PREMIUM_FILES/<span class="style13"><strong>downloads/racing</strong></span>/* location, <span class="style14"><strong>including all subdirectories</strong></span>, starting with the<span class="style17"> date of purchase (today)</span> + <span class="style18"><strong>2 more days</strong></span>. <br />
      Date of files must be encoded in format: YYYY-MM-DD and will be derived from the file name: <span class="style19">Some_File__<strong>2010-08-23</strong>.pdf</span></span></td>
    </tr>
    <tr>
      <td width="120" style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;"><b>Product name</b></td>
      <td><div style="background-color:#FFC;padding:3px;" align="center"><span class="style12">Last 2 weeks archive [fs-daterange:<span class="style13">allfiles/content</span>:<span class="style15">today</span>:<span class="style18">-2 weeks</span>]</span></div></td>
    </tr>
    <tr>
      <td style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;">Description</td>
      <td style="border-bottom:2px solid gray;"><span class="style9" style="line-height:12px;">Gives access to total of 2 weeks worth of files uploaded into PREMIUM_FILES/<span class="style13"><strong>allfiles/content</strong></span>/* location, <span class="style14"><strong>NOT including subdirectories</strong></span>, starting 2 weeks before the<span class="style17"> date of purchase (today)</span> and <strong>not</strong> including today</td>
    </tr>
    <tr>
      <td width="120" style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;"><b>Product name</b></td>
      <td><div style="background-color:#FFC;padding:3px;" align="center"><span class="style12">Advanced yearly set of courses [fs-daterange:<span class="style13">courses/advanced/data</span>:<span class="style15">2010-08-15</span>:<span class="style18">=1 year</span><span class="style14">:s,<span class="style21">ft</span></span>]</span></div></td>
    </tr>
    <tr>
      <td style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;">Description</td>
      <td style="border-bottom:2px solid gray;"><span class="style9" style="line-height:12px;">Gives access to exactly 1 year worth of files uploaded into PREMIUM_FILES/<span class="style13"><strong>courses/advanced/data</strong></span>/* location, <span class="style14"><strong>including all subdirectories</strong></span>, starting from (and including)<span class="style17"> August 15, 2010</span>.<br />
          <span class="style23">Date of file will be determined from the filesystem (using PHP filectime() function)<span class="style19"></span></span></span></td>
    </tr>
    <tr>
      <td width="120" style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;"><b>Product name</b></td>
      <td><div style="background-color:#FFC;padding:3px;" align="center"><span class="style12">Summer 2010 options trading tutorials [fs-daterange:<span class="style13">trading/options/calls</span>:<span class="style15">2010-06-01</span>:<span class="style15">2010-08-31</span><span class="style14">:s</span>]</span></div></td>
    </tr>
    <tr>
      <td style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;">Description</td>
      <td style="border-bottom:2px solid gray;"><span class="style9" style="line-height:12px;">Gives access to files uploaded into PREMIUM_FILES/<span class="style13"><strong>trading/options/calls</strong></span>/* location, <span class="style14"><strong>including all subdirectories</strong></span>, starting from (and including)<span class="style17"> June 01, 2010</span> and ending at (and including) <span class="style17">August 31, 2010</span><br />
      Date of files must be encoded in format: YYYY-MM-DD and will be derived from the file name: <span class="style19">Some_File__<strong>2010-08-23</strong>.pdf <span class="style24">(this is default setting, unless 'ft' attribute is specified)</span></span></span></td>
    </tr>
    <tr>
      <td width="120" style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;"><b>Product name</b></td>
      <td><div style="background-color:#FFC;padding:3px;" align="center"><span class="style12">1 Day Access Pass [fs-daterange:<span class="style13">markets/alerts/stocks</span>:<span class="style15">today</span>:<span class="style18">=1 day</span>]</span></div></td>
    </tr>
    <tr>
      <td style="text-align:center;background-color:#DDD;padding:3px;font-size:12px;">Description</td>
      <td style="border-bottom:2px solid gray;"><span class="style9" style="line-height:12px;">Gives access to 1 day worth of files uploaded into PREMIUM_FILES/<span class="style13"><strong>market/alerts/stocks</strong></span>/* location, <span class="style14"><strong>NOT including subdirectories</strong></span>, released on the <span class="style17"> date of purchase (today)</span></td>
    </tr>

</table>
</div>

<?php
}
//===========================================================================

?>