<?php

include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

// Security check. This function will return only if security check passed.
MWX__ValidateURLSecurityParam();

if (!isset($_POST) || !count($_POST))
   exit ("Cannot call this file directly.");

$mwx_settings = MWX__get_settings ();

if (isset($_POST['pay_affiliate']))
   {
   $due_affiliate_id = array_keys($_POST['pay_affiliate']);
   $due_affiliate_id = $due_affiliate_id[0];
   }
else
   exit ('Bad request');

// Returns: array ('aff_id'=>123, 'aff_email'=>'', 'due_payout_amt'=>'123.45');
$aff_due_info = MWX__CalculateDuePayoutForAffiliate ($mwx_settings, $due_affiliate_id);
if ($aff_due_info['due_payout_amt'] <= 0)
   MWX__ErrorExit (__FILE__, __LINE__, "Cannot pay '{$aff_due_info['due_payout_amt']}' to affiliate. Invalid amount");

MWX__log_event (__FILE__, __LINE__, "Paying '\${$aff_due_info['due_payout_amt']}' to affiliate: {$aff_due_info['aff_email']} ...");

// Call MemberWing-X Affiliate Network Payment Server. If successful - redirect payer(merchant) directly to paypal
// array (
//    "http://sandbox|live.memberwingx.com/affiliate-network/pay",
//    "http://sandbox|live.memberwing-x.com/affiliate-network/pay",
//    );
//
foreach (MWX__GetMWXAN_API_Endpoints($mwx_settings) as $api_endpoint)
   {
   // Execute Paypal Adaptive Chained Payment API:
   $return_data = MWX__CallMWXANServer_AffiliatePayout ($api_endpoint, $mwx_settings, $aff_due_info);
   if ($return_data['mwxan_response']['curl_error_no'] || !isset($return_data['mwxan_response']['paypal_response']['payKey']))
      {
      MWX__log_event (__FILE__, __LINE__, "ERROR calling MemberWing Affiliate Network API endpoint $api_endpoint. Error: '" . $return_data['mwxan_response']['curl_error_msg'] . "'");
      // Redundancy measure in case one endpoint server is unavailable
      continue;
      }
   else
      {
      break;
      }
   }

if ($return_data['mwxan_response']['curl_error_no'])
   MWX__ErrorExit (__FILE__, __LINE__, "Aborting...");   // Note: detailed message already logged above.

if ($return_data['mwxan_response']['paypal_response']['curl_error_no'])
   MWX__ErrorExit (__FILE__, __LINE__, "Error executing curl/calling paypal API endpoint: " . '<span style="color:red">' . htmlentities($return_data['mwxan_response']['paypal_response']['curl_error_msg']) . '</span>');

if (!isset($return_data['mwxan_response']['paypal_response']['payKey']) || $return_data['mwxan_response']['paypal_response']['paymentExecStatus'] != 'CREATED')
   {
   $error_msg="Paypal Adaptive API call failed. Expected 'CREATED' and valid 'payKey'.\nPaypal data response:";
   if (@is_array($return_data['mwxan_response']['paypal_response']))
      {
      foreach ($return_data['mwxan_response']['paypal_response'] as $k=>$v)
         $error_msg .= "<br />&nbsp;&nbsp;&nbsp;$k=$v";
      }
   $error_msg .= "<br /><br /><h3>Please save this error and <a href='" . get_bloginfo ('wpurl') . "'>contact merchant support</a></h3>";
   MWX__ErrorExit (__FILE__, __LINE__, $error_msg);
   }

MWX__log_event (__FILE__, __LINE__, "SUCCESS-1: Paypal Adaptive call to $api_endpoint for parallel payment (affiliate payment) succeeded. payKey retrieved: {$return_data['mwxan_response']['paypal_response']['payKey']}. Redirecting buyer to Paypal site for final payment...");

// We got valid paykey here. Redirect buyer to Paypal site to complete purchase.
MWX__RedirectToPayPal ($mwx_settings, $return_data['mwxan_response']['paypal_response']['payKey']);
exit ();


//===========================================================================
//
// Executes call to Paypal Adaptive API to pay affiliate and MWXAN
// @return array of results: Paypal return data and curl error code/message
//
// $aff_due_info = array ('aff_id'=>123, 'aff_email'=>'', 'due_payout_amt'=>'123.45');

function MWX__CallMWXANServer_AffiliatePayout ($api_endpoint, $mwx_settings, $aff_due_info)
{
   // Setting the curl parameters.
   $ch = curl_init ();
   curl_setopt($ch, CURLOPT_URL,             $api_endpoint);
   curl_setopt($ch, CURLOPT_VERBOSE,         1);

   // Turning off the server and peer verification (TrustManager Concept).
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);

   curl_setopt($ch, CURLOPT_RETURNTRANSFER,  1);
   curl_setopt($ch, CURLOPT_POST,            1);

   $ipn_url = $mwx_settings['paypal_ipn_url'] . '?' . MWX__URL_DebugStr ($mwx_settings);

   $custom_data = MWX__PackCustomData2 ($mwx_settings['secret_password'], $aff_due_info['aff_id'], $aff_due_info['due_payout_amt']);

   // Paypal params
   $nvp_request_paypal_arr  = array (
      "currencyCode"                         => urlencode($mwx_settings['paypal_currency_code']),
      "memo"                                 => urlencode('Affiliate payment from ' . get_bloginfo ('wpurl')),
      "receiverList.receiver(0).email"       => urlencode($aff_due_info['aff_email']),
      "receiverList.receiver(0).amount"      => urlencode($aff_due_info['due_payout_amt']),
      "receiverList.receiver(0).primary"     => "false",
      "ipnNotificationUrl"                   => urlencode($ipn_url),
      "cancelUrl"                            => urlencode(@$_SERVER['HTTP_REFERER']),
      "returnUrl"                            => urlencode(@$_SERVER['HTTP_REFERER']),
      "trackingId"                           => urlencode(MWX__RepackCustomData($custom_data)),  // Repack custom data to add randomization to it.
      );


   // MWX params
   $nvp_request_mwx_arr  = array (
      "payment_event_type"                   => 'aff_payout',        // 'purchase' or 'aff_payout'
      "mwx_version"                          => MEMBERWING_X_VERSION,
      "mwx_edition"                          => MEMBERWING_X_EDITION,
      "is_sandbox"                           => $mwx_settings['paypal_sandbox_enabled'],
      "license_code"                         => urlencode ($mwx_settings['memberwing-x-license_code']),
      "license_domain"                       => urlencode ($_SERVER['HTTP_HOST']),
      "admin_email"                          => MWX__Get_Admin_Email(),
      );

   curl_setopt($ch, CURLOPT_POSTFIELDS, 'nvp_request_paypal=' . urlencode(serialize($nvp_request_paypal_arr)) . '&nvp_request_mwx=' . urlencode(serialize($nvp_request_mwx_arr)));

   // Executing request...
   $curl_response = curl_exec($ch);

   $call_results = array (
      'mwxan_response'  => @unserialize($curl_response),
      'curl_error_no'   => curl_errno ($ch), // Returns the error number or 0 (zero) if no error occurred.
      'curl_error_msg'  => curl_error ($ch), // Returns the error message or '' (the empty string) if no error occurred.
      );

   // Closing curl
   curl_close($ch);

   return $call_results;
}
//===========================================================================

?>