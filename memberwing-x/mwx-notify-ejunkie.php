<?php

/*
-  E-junkie integration help info:
   http://www.e-junkie.com/ej/help.integration.htm

To have order data sent to the same URL for all purchases, you can specify your URL in Seller Admin > Account Preferences as the Common Notification URL.

You can also/instead have order data sent only for purchases of a specific product, or specify a different URL to receive purchase data for each product:

   1. Check Send transaction data to a URL while adding or editing your product;
   2. Press the [Submit] Product button if you are adding the product, or press the NEXT button if you are editing the product;
   3. In the Payment Variable Information URL field, enter the URL of your script that will receive the order data;
   4. Press NEXT button till you can SUBMIT to reach the button code screen;
   5. Use the Buy NOW or E-junkie Cart button codes from this screen to start selling your product (if you had already copy-pasted the button code for this product and made no other changes to the product, your existing code will continue to work fine).


Debugging:
==========
filename.php?XDEBUG_SESSION_START=1
*/

   include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

   MWX__log_event (__FILE__, __LINE__, "Raw Entry Hit. ====================== POST data: =============================\n" . serialize($_POST));

   // Prevent loading of file directly.
   if (!isset($_POST) || !count($_POST))
      {
      exit ('Cannot call this file directly');
      }

   $mwx_settings = MWX__get_settings ();

   if ($mwx_settings['mwx_api_key'] != @$_REQUEST['mwx_api_key'])
      {
      $passed_api_key = @$_REQUEST['mwx_api_key'];
      MWX__log_event (__FILE__, __LINE__, "mwx_api_key is invalid or missing. Passed 'mwx_api_key' value: '$passed_api_key'. Aborting");
      exit();
      }

   //---------------------------------------
   // Assemble proper variables into '$_inputs' array
   //
   $_inputs = array ();
   MWX__ResetInputs ($_inputs);

   $_inputs['item_name']        = isset($_POST['item_name'])?$_POST['item_name']:(isset($_POST['item_name1'])?$_POST['item_name1']:(isset($_POST['memo'])?$_POST['memo']:(@$_POST['product_name']?$_POST['product_name']:"Unknown Item Name")));
      $_inputs['item_name']         = str_replace ("'", "", $_inputs['item_name']);
   $_inputs['item_number']      = @$_POST['item_number'];
   $_inputs['first_name']       = @$_POST['first_name'];
   $_inputs['last_name']        = @$_POST['last_name'];
   $_inputs['payer_email']      = @$_POST['payer_email'];
   $_inputs['payer_id']         = $_inputs['payer_email'];
   $_inputs['subscr_id']        = "0";
   $_inputs['subscr_date']      = isset($_POST['payment_date'])?$_POST['payment_date']:"now";
      $_inputs['U_txn_date']    = date ('Y-m-d H:i:s T', strtotime (urldecode($_inputs['subscr_date']))); // Normalize it for database usage.
   $_inputs['recurring']        = '0'; // E-junkie does not support recurring subscriptions.
   $_inputs['period3']          = "";
   $_inputs['mc_amount3_gross'] = isset($_POST['mc_gross'])?$_POST['mc_gross']:(isset($_POST['mc_amount3'])?$_POST['mc_amount3']:(isset($_POST['mc_amount2'])?$_POST['mc_amount2']:(isset($_POST['mc_amount1'])?$_POST['mc_amount1']:(@$_POST['payment_gross']?$_POST['payment_gross']:"0")))); // mc_gross is set for 1st notif., mc_amount3 for 2nd.
   $_inputs['mc_currency']      = @$_POST['mc_currency'];
   $_inputs['txn_id']           = @$_POST['txn_id'];
   $_inputs['txn_type']         = @$_POST['txn_type'];
   $_inputs['payment_status']   = strtolower(@$_POST['payment_status']);
   $_inputs['receiver_email']   = @$_POST['business'];
   $_inputs['customer_ip']      = @$_POST['buyer_ip'];
   //---------------------------------------

   MWX__log_event (__FILE__, __LINE__, "Passing assembled _inputs:\n" . serialize($_inputs));

   MWX__TransactionTypeSwitch ();
   MWX__log_event (__FILE__, __LINE__, "Done. ==========================================");

   exit ();

?>