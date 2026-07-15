<?php
/*
   MemberWing-X Extension
   ======================


   NMI Payments will call this script with $_GET params to notify about product purchase event (payment, subscription, etc...)
   Note: other payment events, such as: refund, recurring payments, cancellations, chargebacks are not supported.
         This means that if user purchased product and then refunded - he still will be able to access premium
         content unless you manually remove him from Users list or edit "products owned" inside his user account panel.

   Usage:
      -  ...
*/

// Load everything
include_once (preg_replace ('|(/+[^/]+){3}$|', '/mwx-include-all.php', str_replace ('\\', '/', __FILE__)));

$_extension_name = 'NMI Payments';

MWX__log_event (__FILE__, __LINE__, "$_extension_name: Raw Entry Hit.\n  === POST data: ===\n    " . serialize($_POST) . "\n  === GET data: ===\n    " . serialize($_GET));

// Prevent loading of file directly.
if (!@$_REQUEST['product_description_1'] && !@$_REQUEST['order_description'])
   {
   MWX__log_event (__FILE__, __LINE__, "$_extension_name: Error: Cannot find product description in input params");
   exit ('Cannot load this file directly');
   }

// Load MWX settings
$mwx_settings = MWX__get_settings ();

// Make sure integration is enabled
if (!$mwx_settings['nmi_integration_enabled'])
   {
   $msg = "$_extension_name: NMI Payments integration is not enabled inside of MemberWing-X admin panel settings (MemberWing-X->Integration with other systems). Aborting...";
   MWX__log_event (__FILE__, __LINE__, $msg);
   exit ($msg);
   }

// Make sure security key matches
if (@$_REQUEST['key_id'] != $mwx_settings['nmi_security_key_id'])
   {
   MWX__log_event (__FILE__, __LINE__, "$_extension_name: Security key value: '" . @$_REQUEST['key_id'] . "' does not match the value set in NMI Payment system options in MemberWing-X admin panel. Aborting...");
   exit ("$_extension_name: Invalid security key. Please contact site support. Aborted... ");
   }

// Make sure transaction is success
$responsetext = strtoupper(@$_REQUEST['responsetext']);
if ($responsetext != 'SUCCESS' && $responsetext != 'APPROVED')
   {
   $msg = "$_extension_name: Transaction response text: '" . @$_REQUEST['responsetext'] . "'. Expected: 'SUCCESS' or 'APPROVED'. Aborting...";
   MWX__log_event (__FILE__, __LINE__, $msg);
   exit ($msg);
   }

// Initialize redirects array
$customer_redirects   =  array (
   'default'                     => @$mwx_settings['nmi_thank_you_page_url'],  //'http://www.youwalkaway.com/thank-you',
   );

// Do-load redirects array with custom redirects depending on product purchased (for YWA)
if (file_exists (dirname(__FILE__) . '/_custom.php'))
   {
   include_once (dirname(__FILE__) . '/_custom.php');
   }

// Create global '$_inputs' array and initialize it with default values
$_inputs = array ();
MWX__ResetInputs ($_inputs);

//------------------------------------------
// Initialize $_inputs
$_inputs['item_name']         = @$_REQUEST['order_description'];
if (!$_inputs['item_name'])
   $_inputs['item_name']      = @$_REQUEST['product_description_1'];
$_inputs['first_name']        = isset($_REQUEST['first_name'])?$_REQUEST['first_name']:"";
$_inputs['last_name']         = isset($_REQUEST['last_name'])?$_REQUEST['last_name']:"";
$_inputs['payer_email']       = isset($_REQUEST['email'])?$_REQUEST['email']:"";
$_inputs['subscr_id']         = isset($_REQUEST['orderid'])?$_REQUEST['orderid']:"1";       // Subscription ID. Set it to anything if product is a subscription product.
$_inputs['customer_ip']       = @$_REQUEST['ip_address'];
$_inputs['desired_username']  = @$_REQUEST['product_option_1_1']?$_REQUEST['product_option_1_1']:"";
$_inputs['desired_password']  = @$_REQUEST['product_option_2_1']?$_REQUEST['product_option_2_1']:"";
if (!$_inputs['desired_username'] || !$_inputs['desired_password'])
   {
   $_inputs['desired_username']  = $_inputs['payer_email'];
   $_inputs['desired_password']  = substr(md5(microtime()), -8);
   }

// One notification for 2 events: subscription signup and initial payment. So we need to come up with 2 different transaction ID's.
// Make this transaction ID different frmo the "second one" - that will be recording payment.
$_inputs['txn_id']            = 'S' . (isset($_REQUEST['transactionid'])?($_REQUEST['transactionid']):"0");
$_inputs['txn_type']          = 'subscr_signup';   // Subscription signup

// Unpack possible extra data from 'custom'
if (isset($_REQUEST['custom']))
   $_inputs['custom'] = MWX__UnpackCustomData ($mwx_settings['secret_password'], $_REQUEST['custom']);
else
   $_inputs['custom'] = array();
$_inputs['referred_by_id']    = isset($_inputs['custom']['ari'])?$_inputs['custom']['ari']:"self";       // Refered by this affiliate
//------------------------------------------

MWX__log_event (__FILE__, __LINE__, "Calculated _inputs:\n    " . serialize($_inputs));

if (!$_inputs['payer_email'])
   {
   $msg = "$_extension_name: ERROR: Cannot create user account without email address. Please contact site support. Aborting...";
   MWX__log_event (__FILE__, __LINE__, $msg);
   exit ($msg);
   }

// When 'process_cart' - assume this as a subscription
if (@$_REQUEST['action'] == 'process_cart')
   {
   // Process new subscription
   MWX__TransactionTypeSwitch ();
   }
else
   {
   $_inputs['subscr_id']   = "";

   // Assume this is one-time payment product.
   MWX__log_event (__FILE__, __LINE__, "Note: Skipping creation of new subscription for payment action: " . @$_REQUEST['action'] . " New subscriptions are only created for action = 'process_cart'");
   }

// Process initial payment if any.
if (@$_REQUEST['amount'])
   {
   if (@$_REQUEST['action'] == 'process_cart')
      {
      // Initial payment on Subscription product
      $_inputs['txn_type']          = 'subscr_payment';
      }
   else
      {
      // Fixed product purchase
      $_inputs['txn_type']          = 'cart';
      }

   $_inputs['txn_id']            = isset($_REQUEST['transactionid'])?($_REQUEST['transactionid']):"0";
   $_inputs['payment_status']    = 'completed';
   $_inputs['mc_amount3_gross']  = @$_REQUEST['amount'];

   // Process initial payment
   MWX__TransactionTypeSwitch ();
   }

// Redirect customer to "Thank you" page, unless request came from proxy. Then it will be treated like API request
if (!@$_REQUEST['nmiproxy'])
   {
   //------------------------------------------
   // Redirect customer to proper thank you page.
   $product_name         = $_inputs['item_name'];
   foreach ($customer_redirects as $k=>$v)
      {
      if (strings_are_equal ($product_name, $k))
         {
         // Redirect customer to thank you page.
         MWX__log_event (__FILE__, __LINE__, "Note: customer purchased '$product_name' for '\$" . @$_REQUEST['amount'] . "'. Redirecting customer to 'thank you' page at: '$v'");
         MWX__log_event (__FILE__, __LINE__, "\n==================================\n");
         header("Location: " . $v);
         exit;
         }
      }

   // Redirect customer to default thank you page.
   MWX__log_event (__FILE__, __LINE__, "WARNING: Product name '$product_name' is not in list of standard names/redirects. Redirecting customer to default Thank You page: ". $customer_redirects['default']);
   MWX__log_event (__FILE__, __LINE__, "\n==================================\n");
   header("Location: " . $customer_redirects['default']);
   exit;
   //------------------------------------------
   }

//===========================================================================
// Case-insensitive space-agnostic strings comparison
function strings_are_equal ($str1, $str2)
{
   $str1 = strtolower (str_replace (' ', '', $str1));
   $str2 = strtolower (str_replace (' ', '', $str2));

   return ($str1 == $str2);
}
//===========================================================================

?>