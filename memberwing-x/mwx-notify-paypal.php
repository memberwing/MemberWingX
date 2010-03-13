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

Debugging:
==========
filename.php?XDEBUG_SESSION_START=1
*/

include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

   // Prevent loading of file directly.
   if (!isset($_POST) || !count($_POST))
      {
      MWX__log_event (__FILE__, __LINE__, "Exiting ==========================================");
      exit ('Cannot call this file directly');
      }

   // Pull raw POST data. Paypal send to script invalid-ish post keys that never gets to $_POST var. So we need to pull raw data and build our own copy of $_POST
   $raw_post_data = file_get_contents('php://input');
   $raw_post_array = explode('&', $raw_post_data);
   $_MWX_POST = array();
   foreach ($raw_post_array as $keyval)
      {
      $keyval = explode ('=', $keyval);
      if (count($keyval) == 2)
         $_MWX_POST[$keyval[0]] = urldecode($keyval[1]);
      }
   if (count($_MWX_POST)<3)
      {
      $_MWX_POST = $_POST;
      $original_post_used = TRUE;
      }
   else
      $original_post_used = FALSE;

   MWX__log_event (__FILE__, __LINE__, "Raw Entry Hit. ====================== POST data: =============================\n" . serialize($_MWX_POST));

   // Paypal's IPN Sample
   // read the post from PayPal system and add 'cmd'

   if ($original_post_used)
      {
      $_req = 'cmd=_notify-validate';
      foreach ($_MWX_POST as $key => $value)
         {
         $value = urlencode(stripslashes($value));
         $_req .= "&$key=$value";
         }
      }
   else
      $_req = $raw_post_data . '&cmd=_notify-validate';

   // Load MWX settings
   $mwx_settings = MWX__get_settings ();

   // Create global '$_inputs' array and initialize it with default values
   $_inputs = array ();
   MWX__ResetInputs ($_inputs);

   //---------------------------------------
   // Check for adaptive payments case
   $adaptive_transaction      = FALSE;
   $adaptive_transaction_type = "";
   if (isset($_MWX_POST['action_type']) && $_MWX_POST['action_type']=='PAY' && isset($_MWX_POST['status']) && $_MWX_POST['status']=='COMPLETED' && isset($_MWX_POST['pay_key']) && $_MWX_POST['pay_key'])
      {
      $adaptive_transaction = TRUE;
      if (isset($_MWX_POST['transaction_type']))
         {
         if ($_MWX_POST['transaction_type']=='Adaptive Payment PAY')
            $adaptive_transaction_type = 'A_pay';
         else if ($_MWX_POST['transaction_type']=='Adjustment' && isset($_MWX_POST['reason_code']) && $_MWX_POST['reason_code']=='Refund')
            $adaptive_transaction_type = 'A_refund';
         }
      else
         $adaptive_transaction_type = 'A_unknown';

      // Unpack possible extra data from 'custom'
      //    't:1262879366,ip:123.123.123.123,aff_id:aff@blah.com|12345678'
      if (isset($_MWX_POST['tracking_id']))
         $_inputs['custom'] = MWX__UnpackCustomData ($mwx_settings['secret_password'], $_MWX_POST['tracking_id']);
      else
         $_inputs['custom'] = array();
      }
   else
      {
      // Unpack possible extra data from 'custom'
      if (isset($_MWX_POST['custom']))
         $_inputs['custom'] = MWX__UnpackCustomData ($mwx_settings['secret_password'], $_MWX_POST['custom']);
      else
         $_inputs['custom'] = array();
      }

   MWX__log_event (__FILE__, __LINE__, "Custom data event type = '{$_inputs['custom']['evt']}'");
   //---------------------------------------

   //---------------------------------------
   // Sanitize posted variables into '$_inputs' array
   //
   $_inputs['item_name']         = isset($_MWX_POST['item_name'])?$_MWX_POST['item_name']:(isset($_MWX_POST['item_name1'])?$_MWX_POST['item_name1']:(isset($_MWX_POST['memo'])?$_MWX_POST['memo']:"Unknown Item Name"));
   $_inputs['first_name']        = isset($_MWX_POST['first_name'])?$_MWX_POST['first_name']:"";
   $_inputs['last_name']         = isset($_MWX_POST['last_name'])?$_MWX_POST['last_name']:"";
   $_inputs['payer_email']       = isset($_MWX_POST['payer_email'])?$_MWX_POST['payer_email']:(isset($_MWX_POST['sender_email'])?$_MWX_POST['sender_email']:"");
   $_inputs['payer_id']          = isset($_MWX_POST['payer_id'])?$_MWX_POST['payer_id']:"";
   $_inputs['subscr_id']         = isset($_MWX_POST['subscr_id'])?$_MWX_POST['subscr_id']:'0';   // Only present for subscriptions. Unique for every act of subscription, regarldess of payer.
   $_inputs['subscr_date']       = isset($_MWX_POST['subscr_date'])?$_MWX_POST['subscr_date']:"now";
      $_inputs['U_txn_date']     = date ('Y-m-d H:i:s T', strtotime (urldecode($_inputs['subscr_date']))); // Normalize it for database usage.
   $_inputs['recurring']         = isset($_MWX_POST['recurring'])?$_MWX_POST['recurring']:NULL;
   $_inputs['period3']           = isset($_MWX_POST['period3'])?$_MWX_POST['period3']:NULL;
   $_inputs['payment_status']    = isset($_MWX_POST['payment_status'])?strtolower($_MWX_POST['payment_status']):($adaptive_transaction?"completed":"unknown");
   $_inputs['mc_amount3_gross']  = isset($_MWX_POST['mc_gross'])?$_MWX_POST['mc_gross']:(isset($_MWX_POST['mc_amount3'])?$_MWX_POST['mc_amount3']:(isset($_MWX_POST['mc_amount2'])?$_MWX_POST['mc_amount2']:(isset($_MWX_POST['mc_amount1'])?$_MWX_POST['mc_amount1']:"0"))); // mc_gross is set for 1st notif., mc_amount3 for 2nd.
   if (!$_inputs['mc_amount3_gross'] && isset($_inputs['custom']['tsa']))
      $_inputs['mc_amount3_gross'] = $_inputs['custom']['tsa']; // Passed in some cases, such as when MWX generates pay button and knows total amount of sale.)

   $_inputs['mc_currency']       = isset($_MWX_POST['mc_currency'])?$_MWX_POST['mc_currency']:"";
   $_inputs['txn_id']            = isset($_MWX_POST['txn_id'])?$_MWX_POST['txn_id']:($adaptive_transaction?$_MWX_POST['pay_key']:"");
   $_inputs['parent_txn_id']     = isset($_MWX_POST['parent_txn_id'])?$_MWX_POST['parent_txn_id']:($adaptive_transaction?$_inputs['txn_id']:"");
   $_inputs['txn_type']          = isset($_MWX_POST['txn_type'])?$_MWX_POST['txn_type']:""; // When payment_status = 'Refunded', 'txn_type' is not set.
      if ($_inputs['payment_status'] == 'refunded' || $_inputs['payment_status'] == 'reversed' || $adaptive_transaction_type == 'A_refund')
         $_inputs['txn_type'] = "refund";
      if ($adaptive_transaction && !$_inputs['txn_type'])
         $_inputs['txn_type'] = $adaptive_transaction_type;
   $_inputs['receiver_email']    = isset($_MWX_POST['receiver_email'])?$_MWX_POST['receiver_email']:"";
   $_inputs['pdc_secret']        = isset($_MWX_POST['pdc_secret'])?$_MWX_POST['pdc_secret']:"";   // "IPN Secret Code" from "Edit Product" screen of PayDotCom
   $_inputs['customer_ip']       = isset($_inputs['custom']['ip'])?$_inputs['custom']['ip']:""; // IP addess passed from "pay" page.
   $_inputs['referred_by_id']    = isset($_inputs['custom']['ari'])?$_inputs['custom']['ari']:"self";       // Refered by this affiliate
   $_inputs['pay_key']           = $adaptive_transaction?$_MWX_POST['pay_key']:"";
   $_inputs['aff_paid']          = ($adaptive_transaction_type=='A_pay')?$_inputs['txn_id']:FALSE;        // Affiliate is paid via instant Adaptive Chained Payment Method.
   $_inputs['aff_refunded']      = FALSE;    // Refund was taken from Affiliate via Adaptive Refund Method. Might be possible if: Original purchase was Adaptive/Chained + Adaptive refunding was used + Affiliate has API agreement with seller.
   if ((isset($mwx_settings['paypal_sandbox_enabled']) && $mwx_settings['paypal_sandbox_enabled']) || $_SERVER['REMOTE_ADDR']=='216.113.191.33')
      $_inputs['is_sandbox'] = TRUE;
   else
      $_inputs['is_sandbox'] = FALSE;
   //---------------------------------------

   //---------------------------------------
   // Form desired username and password.
   $_inputs['desired_username']  = "";
   $_inputs['desired_password']  = "";

   ///!!! NOTE: Add to cart payment option is not yet supported. Code needs to be modify to accept multiple items in single payment notification.

   // Search for keyname matching 'option_name*' that has a value matching '*pass*'
   foreach ($_MWX_POST as $k=>$v)
      {
      if (!strncmp ($k, "option_name", 11) && stristr ($v, 'pass'))
         {
         // $k = option_nameX[_Y] => option_selectionX[_Y]
         $password_key = str_replace ('option_name', 'option_selection', $k);
         $username_key = $password_key;

         $i=$username_key[16];
         if ($i)
            {
            $username_key[16] = $i-1;

            $_inputs['desired_username'] = $_MWX_POST[$username_key];
            $_inputs['desired_password'] = $_MWX_POST[$password_key];

            // if option_name1_8 - then use $_MWX_POST['item_name8'] for item name.
            if (isset($k[13]) && isset($_MWX_POST['item_name'.$k[13]]))
               $_inputs['item_name'] = $_MWX_POST['item_name'.$k[13]];

            break;
            }
         }
      }

   if (!$_inputs['desired_username'])
      $_inputs['desired_username'] = $_inputs['payer_email'];
   if (!$_inputs['desired_username'])
      $_inputs['desired_username'] = strtolower($_inputs['first_name'] . $_inputs['last_name']);
   if (!$_inputs['desired_username'])
      $_inputs['desired_username'] = 'user';

   if (!$_inputs['desired_password'])
      $_inputs['desired_password'] = substr(md5(microtime()), -8);
   //---------------------------------------

   //---------------------------------------
   // Debugging log.
   MWX__log_event (__FILE__, __LINE__, "*** Transaction Detected *** : ({$_inputs['txn_type']} from {$_inputs['payer_email']})", $_req);
   //---------------------------------------

   //---------------------------------------
   // Now paypal requires this for "everything that hits the script". See:
   // http://paypaldeveloper.com/pdn/board/message?board.id=ipn&thread.id=19454&view=by_date_ascending&page=1
   //
   header("HTTP/1.0 200 OK");
   //---------------------------------------

   // Paypal sandbox IP = '216.113.191.33'
   if ($_inputs['is_sandbox'])
      {
      // Sandbox version
      MWX__log_event (__FILE__, __LINE__, "Note: Will be using Paypal Sandbox option - testing mode");
      $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
      }
   else
      {
      // No Sandbox version
      $fp = fsockopen ('ssl://www.paypal.com',         443, $errno, $errstr, 30);
      }

   if (!$fp)
      {
      // HTTP ERROR
      MWX__log_event (__FILE__, __LINE__, "Error: Couldn't reply to PayPal due to 'fsockopen' error $errno($errstr). IPN Aborted. (txn_type={$_inputs['txn_type']} from {$_inputs['payer_email']} for {$_inputs['item_name']})");
      MWX__log_event (__FILE__, __LINE__, "Done. ==========================================");
      exit ();
      }

   MWX__log_event (__FILE__, __LINE__, "About to send back to Paypal: $_req");

   // post back to PayPal system to validate
   $header =  "POST /cgi-bin/webscr HTTP/1.0\r\n";
   $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
   $header .= "Content-Length: " . strlen($_req) . "\r\n\r\n";

   $iBytesWritten = fputs ($fp, $header . $_req);
   if ($iBytesWritten === FALSE)
      {
      // Problem.
      MWX__log_event (__FILE__, __LINE__, "Error: Couldn't reply to PayPal due to 'fputs' error. IPN Aborted. (txn_type={$_inputs['txn_type']} from {$_inputs['payer_email']} for {$_inputs['item_name']})");
      }

   while (!feof($fp))
      {
      $res = fgets ($fp, 1024);
      if (strtoupper($res) == "VERIFIED")
         {
         MWX__log_event (__FILE__, __LINE__, "Note: Received '$res' after sending POST back to Paypal. (txn_type={$_inputs['txn_type']} from {$_inputs['payer_email']} for {$_inputs['item_name']})");
         MWX__TransactionTypeSwitch ();
         break;
         }
      else if (strtoupper($res) == "INVALID")
         {
         // log for manual investigation
         MWX__log_event (__FILE__, __LINE__, "Problem: Received '$res' after sending POST back to Paypal. (txn_type={$_inputs['txn_type']} from {$_inputs['payer_email']} for {$_inputs['item_name']})");

         // temporary measure while INVALID reply is sorted out with Paypal.
         // if ($adaptive_transaction)
         //    {
         //    MWX__log_event (__FILE__, __LINE__, "NOTE: Ignoring '$res' reply from Adaptive IPN postback/Paypal. Processing request...");
         //    MWX__TransactionTypeSwitch ();
         //    }

         break;
         }
      else
         {
         // Skip logging it - too much cookie/headers data coming in...
         }
      }

   fclose ($fp);
   MWX__log_event (__FILE__, __LINE__, "Done. ==========================================");

?>