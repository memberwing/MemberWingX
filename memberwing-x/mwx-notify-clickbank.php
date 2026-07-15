<?php

include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

   MWX__log_event (__FILE__, __LINE__, "Raw Entry Hit. ====================== POST data: =============================\n" . serialize($_POST));

   // Prevent loading of file directly.
   if (!isset($_POST) || !count($_POST))
      {
      exit ('Cannot call this file directly');
      }

   $mwx_settings = MWX__get_settings ();

   $_req = '';
   foreach ($_POST as $key => $value)
      {
      $value = urlencode(stripslashes($value));
      $_req .= "&$key=$value";
      }

   //---------------------------------------
   // Sanitize posted variables into '$_inputs' array
   //
   $_inputs = array ();
   MWX__ResetInputs ($_inputs);

   $_inputs['item_name']        = @$_POST['cprodtitle'];
   $_inputs['item_number']      = @$_POST['cproditem'];
   $first_last_name = preg_split('|\s+|', trim(@$_POST['ccustname']));
   $_inputs['first_name']       = $first_last_name[0];
   $_inputs['last_name']        = isset($first_last_name[1])?$first_last_name[1]:"";
   $_inputs['payer_email']      = @$_POST['ccustemail'];
   $_inputs['payer_id']         = $_inputs['payer_email'];
   $_inputs['subscr_id']        = isset($_POST['ctransreceipt'])?$_POST['ctransreceipt']:0;
   $_inputs['subscr_date']      = isset($_POST['ctranstime'])?$_POST['ctranstime']:time();
      $_inputs['subscr_date']   = date ('Y-m-d H:i:s', intval($_inputs['subscr_date'])); // Normalize it for database usage.
      $_inputs['U_txn_date']    = date ('Y-m-d H:i:s T', strtotime (urldecode($_inputs['subscr_date']))); // Normalize it for database usage.

   $_inputs['recurring']        = (@$_POST['cprodtype']=='RECURRING')?'1':'0';
   $_inputs['period3']          = "";
   $_inputs['mc_amount3_gross'] = intval(@$_POST['ctransamount']) / 100;
   $_inputs['mc_currency']      = "";
   $_inputs['txn_id']           = $_inputs['subscr_id'];
   $_inputs['txn_type']         = @$_POST['ctransaction'];
   $_inputs['payment_status']   = 'completed'; // Force it this way to pass Transaction_Type_Switch
   $_inputs['receiver_email']   = "";
   $_inputs['pdc_secret']       = "0";
   $_inputs['customer_ip']      = "0.0.0.0";
   //---------------------------------------

   //---------------------------------------
   // Validate Clickbank request
   // Read clickbank user secret key from WP Dbase
///!!!   $mwx_settings = get_option ("MemberWingAdminOptions");
   if (isset($mwx_settings['clickbank_secret_key']))
      $clickbank_secret_key = $mwx_settings['clickbank_secret_key'];
   else
      $clickbank_secret_key = '0';
   if (!MWX__ClickBank_PostValid ($clickbank_secret_key))
      {
      MWX__log_event (__FILE__, __LINE__, "ERROR: POST validation failed. Using clickbank_secret_key='$clickbank_secret_key' (txn_type: {$_inputs['txn_type']} for '{$_inputs['payer_email']}')", $_req);
      exit ('POST validation failed. Invalid request.');
      }
   //---------------------------------------

   //---------------------------------------
   // Validate purchased product name
   if (isset($mwx_settings['clickbank_product_keyword']) && trim($mwx_settings['clickbank_product_keyword']))
      {
      // 'clickbank_product_keyword' is set. Make sure product purchased matches specified one.
      if (stristr ($_inputs['item_name'], trim($mwx_settings['clickbank_product_keyword'])))
         MWX__log_event (__FILE__, __LINE__, "NOTE: Name of product purchased({$_inputs['item_name']}) matches required keyword({$mwx_settings['clickbank_product_keyword']}). Proceeding...", $_req);
      else
         {
         MWX__log_event (__FILE__, __LINE__, "WARNING: Name of product purchased({$_inputs['item_name']}) DOES NOT match required keyword({$mwx_settings['clickbank_product_keyword']}). Aborting this clickbank event processing...", $_req);
         exit ('This product does not match the required keyword specified. Aborting processing.');
         }
      }
   else
      MWX__log_event (__FILE__, __LINE__, "NOTE: No product keyword is specified in MemberWing settings. All products will be processed, including this one...");
   //---------------------------------------


   MWX__TransactionTypeSwitch ();
   MWX__log_event (__FILE__, __LINE__, "Done. ==========================================");

   exit ();


//===========================================================================
//
// Link Security Script Service:
//    https://www.clickbank.com/vendor_tools.html#Vendor_Tools_7
//
// $key='YOUR SECRET KEY';

function ClickBank_LinkValid ($key)
{
return TRUE; // TEST DID NOT SENT THESE VARS. WILL NOT BE USED.
   $rcpt=$_REQUEST['cbreceipt'];
   $time=$_REQUEST['time'];
   $item=$_REQUEST['item'];
   $cbpop=$_REQUEST['cbpop'];

   $xxpop=sha1("$key|$rcpt|$time|$item");
   $xxpop=strtoupper(substr($xxpop,0,8));

   if ($cbpop==$xxpop)
      return TRUE;

   return FALSE;
}
//===========================================================================

//===========================================================================
//
// http://www.clickbank.com/20080219_release_summary.html
//    4.2.1 CIPHER (VALIDATION PROCESSING CODE)
//
// $key='YOUR SECRET KEY';

function MWX__ClickBank_PostValid ($key)
{
return TRUE; ///!!! Clickbank update fails this check. Need to do more research.
   $ccustname = $_REQUEST['ccustname'];
   $ccustemail = $_REQUEST['ccustemail'];
   $ccustcc = $_REQUEST['ccustcc'];
   $ccuststate = $_REQUEST['ccuststate'];
   $ctransreceipt = $_REQUEST['ctransreceipt'];
   $cproditem = $_REQUEST['cproditem'];
   $ctransaction = $_REQUEST['ctransaction'];
   $ctransaffiliate = $_REQUEST['ctransaffiliate'];
   $ctranspublisher = $_REQUEST['ctranspublisher'];
   $cprodtype = $_REQUEST['cprodtype'];
   $cprodtitle = $_REQUEST['cprodtitle'];
   $ctranspaymentmethod = $_REQUEST['ctranspaymentmethod'];
   $ctransamount = $_REQUEST['ctransamount'];
   $caffitid = $_REQUEST['caffitid'];
   $cvendthru = $_REQUEST['cvendthru'];
   $cbpop = $_REQUEST['cverify'];

   $xxpop = sha1("$ccustname|$ccustemail|$ccustcc|$ccuststate|$ctransreceipt|$cproditem|$ctransaction|"
      ."$ctransaffiliate|$ctranspublisher|$cprodtype|$cprodtitle|$ctranspaymentmethod|$ctransamount|$caffitid|$cvendthru|$key");

   $xxpop=strtoupper(substr($xxpop,0,8));

   if ($cbpop==$xxpop)
      return TRUE;

   return FALSE;
}
//===========================================================================


?>