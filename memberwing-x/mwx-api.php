<?php
/*

   MemberWing-X API Interface with third party services and applications
   Syntax for POST request:

   POST URL (your full URL to mwx-api.php):
      http://WWW.YOUR-SITE.COM/wp-content/plugins/memberwing-x/mwx-api.php

   Input Params:
      'api_key'      -  mandatory: API key specified in MemberWing-X Settings->General

      'reply_format' -  optional: 'php', 'json'. Default: 'php' (PHP serialized associative array)

      'action'       -  mandatory: Main "switch". Possible values:
         'create_affiliate'   -  To create new affialite account or, if already exists, get AffNet ID of existing account
                                 Sub-parameters:
            'email'              - mandatory: Affiliate email.
            'desired_username'   - optional:  Desired username. If missing - 'email' will be used as ID.
            'desired_password'   - optional:  Desired password. If missing - new password will be generated. If account already exists - no changes.
            'is_sandbox'         - optional:  Default: '0'. '1' - means account is sandbox (testing) account. No real money payments will go to his account.

         'build_affiliate_link' - Create affiliate link for already existing affiliate
            'destination_url'    -  Merchant page on merchant's site - final original destination
            'aff_id' OR 'aff_id1'-  mandatory: main tier affiliate ID as returned by above 'create_affiliate' call to affiliate network
            'aff_id2'            -  optional: tier2 affiliate ID as returned by 'create_affiliate' call to affiliate network
            'aff_id3'            -  optional: tier3 affiliate ID as returned by 'create_affiliate' call to affiliate network
            'aff_id4'            -  optional: tier4 affiliate ID as returned by 'create_affiliate' call to affiliate network
            'aff_id5'            -  optional: tier5 affiliate ID as returned by 'create_affiliate' call to affiliate network

         'get_afnet_settings' -  Retrieve affiliate network settings
         'get_members_info'   -  Retrieve detailed information about all members, products owned and affiliate referral information.


   Examples:
   =========
   http://YOUR-SITE.com/wordpress/wp-content/plugins/memberwing-x/mwx-api.php?api_key=f205a85e67c6052e&action=build_affiliate_link&destination_url=http://YOUR-SITE.com/wordpress/product-1/&aff_id=24&aff_id2=23&aff_id3=29&reply_format=json
*/

// Set to '1' to allow API request via GET. Use for quick testing only!
// Do not forget to set this option back to '0' after testing finished, otherwise it will be security hole!
// Note: For testing with GET api_key must be equal to last 4 digits of actual API key. DO NOT PASS real API key with GET requests.
// **********************
$_allow_get_requests = 0;
// **********************

if (@$_allow_get_requests)
   $_INPUT_REQUEST = $_REQUEST;
else
   $_INPUT_REQUEST = $_POST;

// Decode inputs
foreach ($_INPUT_REQUEST as $key => $value)
   {
   $_INPUT_REQUEST[$key] = urldecode(stripslashes($value));
   }

include_once (dirname(__FILE__) . '/mwx-include-all.php');

// Prevent calling of this file directly.
if (!isset($_INPUT_REQUEST) || !count($_INPUT_REQUEST))
   {
   MWX__log_event (__FILE__, __LINE__, "===== MWX API Request: direct GET call attempt. Exiting");
   exit ('Cannot call this file directly');
   }

// Log raw entry data
MWX__log_event (__FILE__, __LINE__, "===== MWX API Request: RAW Entry hit:\n   " . serialize($_INPUT_REQUEST));

// Load MWX settings
$mwx_settings = MWX__get_settings ();

$reply_data = MWX_ProcessAPIRequest ($mwx_settings);

// Encode reply data according to requested 'reply_format'
switch (@$_INPUT_REQUEST['reply_format'])
   {
   case 'json' :
      $reply_string = json_encode ($reply_data);
      break;

   case 'php'  :
   default     :
      $reply_string = serialize ($reply_data);
      break;
   }

header ('Content-type: text/css');
echo $reply_string;
exit;

//===========================================================================
// Main API processing function
function MWX_ProcessAPIRequest ($mwx_settings)
{
   global   $_INPUT_REQUEST;
   global   $_allow_get_requests;

   if (@$_allow_get_requests)
      $api_key = substr($mwx_settings['mwx_api_key'], -4);
   else
      $api_key = $mwx_settings['mwx_api_key'];

   $reply_data = array (
      'error_code'   => "0",     // 0:success, else:error code
      'error_msg'    => "",      // Description of error
      'reply_data'   => array("data"=>"none"), // Request-specific data
      );

   if (@$_INPUT_REQUEST['api_key'] != $api_key)
      {
      $reply_data ['error_code'] = '1';
      if (@$_allow_get_requests)
         $reply_data ['error_msg']  = "Invalid API key. Use 'api_key=last 4 digits of real key' for testing of GET requests";
      else
         $reply_data ['error_msg']  = "Invalid API key. Use 'api_key=...'";
      MWX__log_event (__FILE__, __LINE__, "===== MWX API Request: Invalid API key specified");
      return ($reply_data);
      }

   switch (@$_INPUT_REQUEST['action'])
      {
      case  'create_affiliate'      :
         $aff_data = MWX__CreateNewAffiliate (@$_INPUT_REQUEST['email'], @$_INPUT_REQUEST['desired_username'], @$_INPUT_REQUEST['desired_password'], @$_INPUT_REQUEST['is_sandbox'], TRUE);
         $aff_data['mwx_aff_info']['referrals'] = array();  // Suppress referrals history
         $reply_data ['reply_data'] = $aff_data;
         break;

      case  'build_affiliate_link'  :
         $affiliate_link = trim (@$_INPUT_REQUEST['destination_url'], '&?/');
         if (!$affiliate_link || !(@$_INPUT_REQUEST['aff_id'] || @$_INPUT_REQUEST['aff_id1']))
            {
            MWX__log_event (__FILE__, __LINE__, "===== MWX API Request: 'build_affiliate_link' requested with bad parameters: 'destination_url'={$_INPUT_REQUEST['destination_url']}, 'aff_id'={$_INPUT_REQUEST['aff_id']}. Aborting");
            $reply_data ['error_code'] = '3';
            $reply_data ['error_msg']  = "Bad parameters passed for 'build_affiliate_link' request.";
            break;
            }

         if (strpos ($affiliate_link, '?') === FALSE)
            $affiliate_link .= '/?';
         else
            $affiliate_link .= '&';
         // Append main affiliate's id
         if (isset($_INPUT_REQUEST['aff_id']) && $_INPUT_REQUEST['aff_id'])
            $affiliate_link .= "aff={$_INPUT_REQUEST['aff_id']}";
         else if (isset($_INPUT_REQUEST['aff_id1']) && $_INPUT_REQUEST['aff_id1'])  // Basf request
            $affiliate_link .= "aff={$_INPUT_REQUEST['aff_id1']}";

         // Append affiliate's id's of tiers.
         if (isset($_INPUT_REQUEST['aff_id2']) && $_INPUT_REQUEST['aff_id2'])
            $affiliate_link .= "&aff2={$_INPUT_REQUEST['aff_id2']}";
         if (isset($_INPUT_REQUEST['aff_id3']) && $_INPUT_REQUEST['aff_id3'])
            $affiliate_link .= "&aff3={$_INPUT_REQUEST['aff_id3']}";
         if (isset($_INPUT_REQUEST['aff_id4']) && $_INPUT_REQUEST['aff_id4'])
            $affiliate_link .= "&aff4={$_INPUT_REQUEST['aff_id4']}";
         if (isset($_INPUT_REQUEST['aff_id5']) && $_INPUT_REQUEST['aff_id5'])
            $affiliate_link .= "&aff5={$_INPUT_REQUEST['aff_id5']}";

         $reply_data ['reply_data']['affiliate_link'] = $affiliate_link;
         break;

      case 'get_afnet_settings'     :
         $reply_data ['reply_data'] = MWX__GetAffiliateNetworkSettings ($mwx_settings);
         break;

      case 'get_members_info'       :
         $reply_data ['reply_data'] = MWX__Get_Users_Data ();
         break;

      default:
         $invalid_action = TRUE;
         MWX__log_event (__FILE__, __LINE__, "===== MWX API Request: invalid 'action'. Aborting");
         $reply_data ['error_code'] = '2';
         $reply_data ['error_msg']  = "Invalid 'action' specified";
         break;
      }

   if (!$invalid_action)
      MWX__log_event (__FILE__, __LINE__, "===== MWX API Request: 'action'='{$_INPUT_REQUEST['action']}'. Replying with:\n   " . serialize($reply_data));

   return ($reply_data);
}
//===========================================================================


?>