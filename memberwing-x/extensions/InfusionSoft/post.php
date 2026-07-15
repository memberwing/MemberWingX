<?php
/*
   MemberWing-X Extension
   ======================

   InfusionSoft will call this script to notify about product purchase event (payment, subscription, etc...)
   Note: other payment events, such as: refund, recurring payments, cancellations, chargebacks are not supported.
         This means that if user purchased product and then refunded - he still will be able to access premium
         content unless you manually remove him from Users list or edit "products owned" inside his user account panel.

   Usage:

   -  Click on product or web form.

   -  Click tab "Actions" or "Setup Actions"

   -  In dropdown choose "Send an http post to another server"

   -  In Post URL - add url of this script and append the name of this product (or form) to query string. Example:
      http://www.YOUR-SITE.com/wp-content/plugins/memberwing-x/extensions/InfusionSoft/post.php?item_name=My+Gold+Membership
      (replace http://www.YOUR-SITE.com - with the correct URL of your wordpress install)

   -  Click [Save] button

   -  When customer buys product or fills in the form:
      -  notification will be sent to this script
      -  new user account will be created
      -  email will be dispatched to user and website administrator with new customer information and login credentials

   -  To create premium article that would allow access only to this customer:
      -  Login to Wordpress admin panel. Create new post or page:
      -  Navigate to Pages->Add new (or Posts->Add new)
      -  Write content of your premium page.
      -  Inside the text of page, after free teaser insert premium marker like this: {{{gold}}} or {{{membership}}}. Such as:
            This is free teaser visible to everyone (and search engines as well)
            {{{gold}}}
            This is premium part visible only to logged on owner of "My Gold Membership" product
      -  MemberWing-X matches string inside the marker - in this case "gold" against list of products that currently logged on user owns.
         If match found (current user owns product with "...gold..." inside the product name) - user will be allowed to read full content of this article.
         If no matches found - user will only see free teaser + link to your subscribe page.
         You will need to create subscribe page if it is not already exists and make sure it's URL is correct in:
            MemberWingX->General Settings->{SUBSCRIBE_URL_PREMIUM}
      -  Note: you may achieve the same premium protection effect by using this marker: {{{membership}}}
         In this case if currently logged on user owns any product with keyword "membership" in it's name - user will be allowed to access current article/page.
      -  Note: keyword matching is case-insensitive.
*/

// Load everything
include_once (preg_replace ('|(/+[^/]+){3}$|', '/mwx-include-all.php', str_replace ('\\', '/', __FILE__)));

$_extension_name = 'InfusionSoft Postback';

// Prevent loading of file directly.
if (!isset($_POST) || !count($_POST))
   {
   MWX__log_event (__FILE__, __LINE__, "$_extension_name: Raw Entry Hit");
   exit ('Cannot load this file directly');
   }

// Load MWX settings
$mwx_settings = MWX__get_settings ();

if (!$mwx_settings['infusionsoft_postback_integration_enabled'])
   {
   MWX__log_event (__FILE__, __LINE__, "$_extension_name: InfusionSoft integration is not enabled inside of MemberWing-X admin panel settings (MemberWing-X->Integration with other systems). Aborting...");
   exit ();
   }

// Create global '$_inputs' array and initialize it with default values
$_inputs = array ();
MWX__ResetInputs ($_inputs);

//------------------------------------------
// Initialize $_inputs
$_inputs['item_name']         = isset($_REQUEST['item_name'])?$_REQUEST['item_name']:"Unknown Product Name";
$_inputs['payer_email']       = isset($_POST['Email'])?$_POST['Email']:"";
$_inputs['txn_id']            = isset($_POST['Id'])?$_POST['Id']:"0";
$_inputs['txn_type']          = 'cart';   // Single purchase
$_inputs['subscr_id']         = '';       // Subscription ID. Set it to anything if product is a subscription product.
$_inputs['payment_status']    = 'completed';
$_inputs['desired_username']  = $_inputs['payer_email'];
$_inputs['desired_password']  = substr(md5(microtime()), -8);
if ((isset($mwx_settings['paypal_sandbox_enabled']) && $mwx_settings['paypal_sandbox_enabled']) || $_SERVER['REMOTE_ADDR']=='216.113.191.33')
   $_inputs['is_sandbox'] = TRUE;
else
   $_inputs['is_sandbox'] = FALSE;
//------------------------------------------

//------------------------------------------
// Guard against duplicate transaction/unprocessed sales for users who already have some products
$datemark = date ('Y-m-d H:i:s T', strtotime ("now"));
$_inputs['txn_id']            .= " " . $datemark;
$_inputs['subscr_id']         .= " " . $datemark;
//------------------------------------------

MWX__log_event (__FILE__, __LINE__, "$_extension_name: Raw Entry Hit.\n  === POST data: ===\n    " . serialize($_POST) . "\n  === GET data: ===\n    " . serialize($_GET) . "\n  === Calculated _inputs: ===\n    " . serialize($_inputs));

if (!$_inputs['payer_email'])
   {
   MWX__log_event (__FILE__, __LINE__, "$_extension_name: ERROR: Cannot create user account without email address. Aborting...");
   exit ();
   }

// Process purchase
MWX__TransactionTypeSwitch ();

?>