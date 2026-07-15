<?php

/*
Custom Parameters:
You may pass in any additional parameters that you may need and they will be returned to you at the end of the sale.
The only restrictions on custom parameters are that they can not share the name of ANY parameter that our system uses, even from the other sets.
Please note that you WILL need a return script set up on the Look and Feel page to receive any of these
parameters back as they are not included in the confirmation emails.
Set Globally:
Account->Site Management->Approved URL

Set per product:
Products->Edit product->Approved URL
*/

   include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

   // List of IP addresses of 2Checkout.com
   $_2C0_IP_List = array (
      '64.128.185.196'
      );

   MWX__log_event (__FILE__, __LINE__, "Raw Entry Hit. ====================== POST data: =============================\n" . serialize($_POST));

   //---------------------------------------
   // Prevent loading of file directly.
   if (!isset($_POST) || !count($_POST))
      {
      exit ('Cannot call this file directly');
      }
   //---------------------------------------

   //---------------------------------------
   // Security check. Make sure request came from 2CO server.
   if (!in_array($_SERVER['REMOTE_ADDR'], $_2C0_IP_List))
      {
      MWX__log_event (__FILE__, __LINE__, "ERROR: Sale/purchase request received from bad IP='{$_SERVER['REMOTE_ADDR']}'. Expected: '64.128.185.196'. Fraud attempt?");

      // Abort.
      exit();
      }
   //---------------------------------------

   //---------------------------------------
   // Assemble proper variables into '$_inputs' array
   //
   $_inputs = array ();
   MWX__ResetInputs ($_inputs);

   $_inputs['item_name']        = @$_POST['item_name_1'];
   $_inputs['item_number']      = @$_POST['item_name_1'] . "(item_number)";
   $_inputs['first_name']       = @$_POST['customer_first_name'];
   $_inputs['last_name']        = @$_POST['customer_last_name'];
   $_inputs['payer_email']      = @$_POST['customer_email'];
   $_inputs['payer_id']         = "{$_inputs['first_name']}-{$_inputs['last_name']}-{$_inputs['payer_email']}";
   $_inputs['subscr_id']        = $_inputs['payer_id'] . '-' . @$_POST['sale_id'];   // Only present for subscriptions. Unique for every act of subscription, regarldess of payer.
   $_inputs['subscr_date']      = @$_POST['timestamp']; // In MySQL format already.
      $_inputs['U_txn_date']     = date ('Y-m-d H:i:s T', strtotime (urldecode($_inputs['subscr_date']))); // Normalize it for database usage.
   $_inputs['recurring']        = '0'; // E-junkie does not support recurring subscriptions.
   $_inputs['period3']          = "";
   $_inputs['mc_amount3_gross'] = @$_POST['item_list_amount_1'];
   $_inputs['mc_currency']      = @$_POST['list_currency'];
   $_inputs['txn_id']           = @$_POST['sale_id'];
   $_inputs['txn_type']         = @$_POST['message_type']; // ORDER_CREATED, REFUND_ISSUED, RECURRING_STOPPED, ...
   $_inputs['payment_status']   = 'completed'; // Force it this way to pass Transaction_Type_Switch
   $_inputs['receiver_email']   = 'Seller email unspecified for 2CO orders';
   $_inputs['customer_ip']      = '0.0.0.0';

   // Normalize case of positive fraud detection
   if ($_inputs['txn_type'] == 'FRAUD_STATUS_CHANGED' && @$_POST['fraud_status'] != 'pass')
      $_inputs['txn_type'] = '__fraud_detected';

   // NOTE:
   // 'merchant_order_id' passed from 'pay' form will become $_POST['vendor_order_id']. And this is the only variable that could be used
   // to pass anything "custom" via 2CO INS callback.

   //---------------------------------------

   MWX__TransactionTypeSwitch ();
   MWX__log_event (__FILE__, __LINE__, "Done. ==========================================");

   exit ();

?>