<?php

/*
   Uses Paypal Adaptive API set to issue chained payment to instantly pay merchant and affiliate together.
*/

include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

// Security check. This function will return only if security check passed.
MWX__ValidateURLSecurityParam();

if (!isset($_POST) || !count($_POST))
   MWX__ErrorExit (__FILE__, __LINE__, "Cannot call this file directly.");

$mwx_settings = MWX__get_settings ();

// array (
//    "http://sandbox|live.memberwingx.com/affiliate-network/pay",
//    "http://sandbox|live.memberwing-x.com/affiliate-network/pay",
//    );
//
foreach (MWX__GetMWXAN_API_Endpoints($mwx_settings) as $api_endpoint)
   {
   // Call MemberWing-X Affiliate Network Payment Server. If successful - redirect payer(merchant) directly to paypal
   $return_data = MWX__CallMWXANServer_ProductPurchase ($api_endpoint, $mwx_settings);
   if ($return_data['mwxan_response']['curl_error_no'] || !isset($return_data['mwxan_response']['paypal_response']['payKey']))
      {
      MWX__log_event (__FILE__, __LINE__, "ERROR calling MemberWing Affiliate Network API endpoint $api_endpoint. Error: '" . $return_data['mwxan_response']['curl_error_msg'] . "'");
      // Redundancy measure in case one endpoint server is unavailable
      continue;
      }
   else
      break;
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

MWX__log_event (__FILE__, __LINE__, "SUCCESS-1: Paypal Adaptive call to $api_endpoint for chained payment succeeded. payKey retrieved: {$return_data['mwxan_response']['paypal_response']['payKey']}. Redirecting buyer to Paypal site for final payment...");

// We got valid paykey here. Redirect buyer to Paypal site to complete purchase.
MWX__RedirectToPayPal ($mwx_settings, $return_data['mwxan_response']['paypal_response']['payKey']);
exit ();

//===========================================================================
//
// Executes call to Paypal Adaptive API to pay merchant and affiliate instantly.
// @return array of results: Paypal return data and curl error code/message

function MWX__CallMWXANServer_ProductPurchase ($api_endpoint, $mwx_settings)
{
   if ($mwx_settings['paypal_sandbox_enabled'])
      {
      $business_email = $mwx_settings['paypal_sandbox_email'];
      }
   else
      {
      $business_email = $mwx_settings['paypal_email'];
      }

   // evt:purchase,ip:99-224-152-162,ari:24-23-29,tsa:100|267fR09AF
   //
   if (isset($_POST['custom']))
      {
      $custom_data = MWX__UnpackCustomData ($mwx_settings['secret_password'], $_POST['custom']);

      if (!isset($custom_data['ari']))
         MWX__ErrorExit (__FILE__, __LINE__, "Custom data is invalid or affiliate id is not set");

      $aff_raw_ids  = explode (',', $custom_data['ari']);   // "Explode" tiers
      $aff_emails   = array();
      $aff_payouts  = array();

      foreach ($aff_raw_ids as $tier=>$aff_raw_id)
         {
         // Determine aff email
         $aff_info = MWX__GetAffiliateInfoByRawID ($aff_raw_id);

         // Affiliate must exist and his account must be active for him to get instantly paid
         if (!@$aff_info['aff_email'] || @$aff_info['mwx_aff_info']['aff_status'] != 'active')
            {
            MWX__log_event (__FILE__, __LINE__, "WARNING: Affiliate ID: '$aff_raw_id' ({$aff_info['aff_email']}) is either invalid or belong to inactive affiliate. Skipping this affiliate from any payment.");
            continue;
            }

         $aff_emails[]  = $aff_info['aff_email'];
         $aff_payouts[] = MWX__CalculateAffiliatePayoutForSale ($aff_raw_id, $custom_data['tsa'], @$aff_info['mwx_aff_info'], $mwx_settings, $tier+1);
         }
      }
   else
      MWX__ErrorExit (__FILE__, __LINE__, "Custom data is not set");


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

   // Paypal params
   $nvp_request_paypal_arr  = array (
      "currencyCode"                         => urlencode(MWX__GetPostVar('currency_code')),
      "memo"                                 => urlencode(MWX__GetPostVar('item_name')),
      "receiverList.receiver(0).email"       => urlencode($business_email),
      "receiverList.receiver(0).amount"      => urlencode(MWX__GetPostVar('amount')),
      "receiverList.receiver(0).primary"     => "true",
      );

   if (@$aff_emails[0])
      $up_to = min ($mwx_settings['aff_tiers_num'], count($aff_emails));
   else
      {
      $up_to = 0;
      MWX__log_event (__FILE__, __LINE__, "WARNING: Affiliate ID(s): '" . $custom_data['ari'] . "' either invalid or belong to inactive affiliate(s). Sending payment to merchant only.");
      }

   if (!$up_to)
      unset ($nvp_request_paypal_arr["receiverList.receiver(0).primary"]); // Cannot determine affiliate email - single payment will be sent to merchant, for this we dont need to mark single receiver as "primary"

   for ($i=0; $i<$up_to; $i++)
      {
      $idx = $i+1;
      $nvp_request_paypal_arr["receiverList.receiver($idx).email"]       = urlencode($aff_emails[$i]);
      $nvp_request_paypal_arr["receiverList.receiver($idx).amount"]      = urlencode($aff_payouts[$i]);
      $nvp_request_paypal_arr["receiverList.receiver($idx).primary"]     = "false";
      }

   // Do-fill array
   $nvp_request_paypal_arr  =
      array_merge (
         $nvp_request_paypal_arr,
         array (
         "ipnNotificationUrl"                   => urlencode($ipn_url),
         "cancelUrl"                            => urlencode($mwx_settings['payment_cancel_page']),
         "returnUrl"                            => urlencode($mwx_settings['payment_success_page']),
         "trackingId"                           => urlencode(MWX__RepackCustomData(MWX__GetPostVar('custom'))),  // Repack custom data to add randomization to it.
         )
      );

   // MWX params
   $nvp_request_mwx_arr  = array (
      "payment_event_type"                   => 'purchase',             // 'purchase' or 'aff_payout'
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

//===========================================================================
function MWX__GetPostVar ($varname)
{
   if (isset($_POST[$varname]))
      $value = urldecode(stripslashes($_POST[$varname]));
   else
      $value = "";

   return $value;
}
//===========================================================================

?>