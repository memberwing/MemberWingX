<?php
/*
      Wordpress-independent functions
*/
/* **************************************************************************
This software is provided "as is" without any express or implied warranties,
including, but not limited to, the implied warranties of merchantibility and
fitness for any purpose.
In no event shall the copyright owner, website owner or contributors be liable
for any direct, indirect, incidental, special, exemplary, or consequential
damages (including, but not limited to, procurement of substitute goods or services;
loss of use, data, rankings with any search engines, any penalties for usage of
this software or loss of profits; or business interruption) however caused and
on any theory of liability, whether in contract, strict liability, or
tort(including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.
To request source code for MemberWing please contact http://www.memberwing.com/contact
************************************************************************** */


//===========================================================================
function MWX__ErrorExit ($_file, $_line, $extra_message)
{
   $error_message =<<<TTT
<html>
   <head>
      <title>Wordpress Membership plugin - MemberWing-X</title>
   </head>
   <body>
      <h1 align="center" style="font-size: 70%;"><a href="http://www.memberwing.com/">Wordpress Membership plugin - MemberWing-X</a></h1>
      <h2>$extra_message</h2>
      <div align="center">
         <h2><a href="{$_SERVER['HTTP_REFERER']}">Back</a></h2>
      </div>
   </body>
</html>
TTT;

   MWX__log_event ($_file, $_line, "ERROR: Adaptive Payment Script: " . strip_tags($extra_message));
   exit ($error_message);
}
//===========================================================================

//===========================================================================
// Redirects buyer to PayPal.com site to complete purchase.
// @param string $pay_key pay key issued by previous adaptive API call.
function MWX__RedirectToPayPal ($mwx_settings, $pay_key)
{
   // Redirect to paypal.com here
   if ($mwx_settings['paypal_sandbox_enabled'])
      $paypal_url = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $pay_key;
   else
      $paypal_url = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=" . $pay_key;

   header("Location: ". $paypal_url);
}
//===========================================================================

//===========================================================================
//
// Get array of MemberWing-X Affiliate Network API Endpoints

function MWX__GetMWXAN_API_Endpoints ($mwx_settings)
{
   if ($mwx_settings['paypal_sandbox_enabled'])
      $api_call_type = 'sandbox';
   else
      $api_call_type = 'live';

   $api_endpoints = array (
      "http://$api_call_type.memberwingx.com/affiliate-network/pay",
      "http://$api_call_type.memberwing-x.com/affiliate-network/pay",
      );

   return ($api_endpoints);
}
//===========================================================================

//===========================================================================
// Allows debug break with ActiveState Komodo Environment

function MWX__URL_DebugStr ($mwx_settings)
{
   if ($mwx_settings['paypal_sandbox_enabled'] && MWX__DebuggingComputer ($mwx_settings))
      return ('XDEBUG_SESSION_START=1');

   return "";
}

// If you use ActiveState Komodo for debugging on your local machine - put your IP address here
// Advanced developers only, don't bother
function MWX__DebuggingComputer ($mwx_settings)
{
   if ($_SERVER['REMOTE_ADDR'] == $mwx_settings['sandbox_machine_ip_address'])
      return TRUE;
   return FALSE;
}
//===========================================================================

//===========================================================================
// Store custom data to be sent with payment button form, Adaptive Pay call or retrieved from IPN
// $aff_raw_id  - ID of affiliate as taken from cookie or from query string. Could be email or numeric ID.
// $total_sale_amt - optional. Will be taken from IPN variables, unless it is missing like with Adaptive IPN - then it will be taken from custom.

function MWX__PackCustomData ($passkey, $aff_raw_id, $total_sale_amt="0")
{
   $aff_raw_id = str_replace (',', '-', $aff_raw_id); // Replace commas with '-'. Commas could separate multi-tiered sequence

   // passkey - security measure against forged/fabricated requests.

   // Pack string
   $custom_data = array (
// Eliminated for now to save space (max 127 chars limit). R-value will do randomization purpose
//      "t1:"             .  time(),                    // Page load time in browser. Page maybe cached.
      "evt:"            .  "purchase",                // Event type: 'purchase',
      "ip:"             .  str_replace ('.', '-', $_SERVER['REMOTE_ADDR']),   // To save space. urlencode() leaves '-' untouched
      "ari:"            .  $aff_raw_id,            // Affiliate's raw ID. Might be multi-tiered, such as: "52,8,132"
      "tsa:"            .  $total_sale_amt,
      );
   $custom_data = implode (',', $custom_data);

   $calc_passkey = substr(md5($custom_data . $passkey), -4);

   // Note: max=256 chars.
   //
   // t:1262879366,ip:123.123.123.123,ari:aff@blah.com,tsa:12.34|12345678
   $custom_data .= "|$calc_passkey" . 'R09AF';  // Append randomization value to make it unique. R... will be replaced by Adaptive call to make sure custom data is really unique.
   return $custom_data;
}
//===========================================================================

//===========================================================================
// Store custom data to be sent with "manual" affiliate payout.
// $aff_raw_id  - ID of affiliate. Numeric ID of already existing affiliate
// $total_sale_amt - optional. Will be taken from IPN variables, unless it is missing like with Adaptive IPN - then it will be taken from custom.

function MWX__PackCustomData2 ($passkey, $aff_id, $aff_payout)
{
   $aff_id = str_replace (',', '-', $aff_id); // Replace commas with '-'. Commas could separate multi-tiered sequence

   // passkey - security measure against forged/fabricated requests.

   // Pack string
   $custom_data = array (
// Eliminated for now to save space (max 127 chars limit). R-value will do randomization purpose
//      "t1:"             .  time(),                    // Page load time in browser. Page maybe cached.
      "evt:"            .  "aff_payout",              // Event type: 'aff_payout',
      "aff_id:"         .  $aff_id,
      "aff_payout:"     .  $aff_payout,
      );
   $custom_data = implode (',', $custom_data);

   $calc_passkey = substr(md5($custom_data . $passkey), -4);

   // Note: max=256 chars.
   //
   // t:1262879366,ip:123.123.123.123,ari:aff@blah.com,tsa:12.34|12345678
   $custom_data .= "|$calc_passkey" . 'R09AF';  // Append randomization value to make it unique. R... will be replaced by Adaptive call to make sure custom data is really unique.
   return $custom_data;
}
//===========================================================================

//===========================================================================
// Unpacks data, validate passkey
function MWX__UnpackCustomData ($passkey, $packed_data)
{
   $custom_data = preg_replace ('@R[0-9A-Fa-f]+$@', '', $packed_data);

   $custom_data = explode ('|', stripslashes($custom_data));
   if (count($custom_data) != 2)
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: Invalid custom data (1).", $packed_data);
      return array();
      }

   $calc_passkey = substr(md5($custom_data[0] . $passkey), -4);
   if ($calc_passkey != $custom_data[1])
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: Invalid custom data - passkeys mistmatch. Possible fraud/spoof? embedded passkey={$custom_data[1]}, calculated passkey=$calc_passkey, custom data:", $packed_data);
      return array();
      }

   // Validation successful
   $custom_data_arr = array();
   foreach (explode(',', $custom_data[0]) as $el)
       {
       $el = explode (':', $el);
       if (count($el) == 2)
           $custom_data_arr[urldecode($el[0])] = urldecode($el[1]);
       }

   // Decode IP address
   if (isset($custom_data_arr['ip']))
      $custom_data_arr['ip'] = str_replace ('-', '.', $custom_data_arr['ip']);

   if (isset($custom_data_arr['aff_id']))
      $custom_data_arr['aff_id'] = str_replace ('-', ',', $custom_data_arr['aff_id']); // Restore possible comma in aff raw id.
   if (isset($custom_data_arr['ari']))
      $custom_data_arr['ari'] = str_replace ('-', ',', $custom_data_arr['ari']);       // Restore possible comma in aff raw id.

   return ($custom_data_arr);
}
//===========================================================================

//===========================================================================
// Change random trailer [R=...]
function MWX__RepackCustomData ($packed_data)
{
   if (preg_match ('@R[0-9A-Fa-f]+$@', $packed_data))
      $custom_data = preg_replace ('@R[0-9A-Fa-f]+$@', 'R' . substr(md5(microtime()), -4), $packed_data);
   else
      $custom_data = $packed_data . 'R' . substr(md5(microtime()), -4);

   return $custom_data;
}
//===========================================================================

//===========================================================================
//
// Function packs response information from Paypal Adaptive reply into assoc array.
// Sample of response:
//    'responseEnvelope.timestamp=2009-12-24T17%3A42%3A12.286-08%3A00&responseEnvelope.ack=Success&responseEnvelope.correlationId=d2e5e7c33db87&responseEnvelope.build=1095776&payKey=AP-9AE02925M23353634&paymentExecStatus=COMPLETED';
function MWX__UnwrapAdaptiveResponse ($response)
{
   $res_arr = array();
   foreach (explode('&', $response) as $el)
       {
       $el = explode ('=', $el);
       if (count($el) == 2)
           $res_arr[urldecode($el[0])] = urldecode($el[1]);
       }
   return ($res_arr);
}
//===========================================================================

//===========================================================================
function MWX__log_event ($filename, $linenum, $message, $extra_text="")
{
   $log_filename   = dirname(__FILE__) . '/__log.php';
   $logfile_header = '<?php header("Location: /"); exit(); ?>' . "\r\n" . '/* =============== MemberWing-X LOG file =============== */' . "\r\n";
   $logfile_tail   = "\r\nEND";

   // Delete too long logfiles.
   if (@file_exists ($log_filename) && @filesize($log_filename)>1000000)
      unlink ($log_filename);

   $filename = basename ($filename);

   if (@file_exists ($log_filename))
      {
      // 'r+' non destructive R/W mode.
      $fhandle = @fopen ($log_filename, 'r+');
      if ($fhandle)
         @fseek ($fhandle, -strlen($logfile_tail), SEEK_END);
      }
   else
      {
      $fhandle = @fopen ($log_filename, 'w');
      if ($fhandle)
         @fwrite ($fhandle, $logfile_header);
      }

   if ($fhandle)
      {
      @fwrite ($fhandle, "\r\n// " . $_SERVER['REMOTE_ADDR'] . '(' . $_SERVER['REMOTE_PORT'] . ')' . ' -> ' . date("Y-m-d, G:i:s") . "|$filename($linenum)|: " . $message . ($extra_text?"\r\n//    Extra Data: $extra_text":"") . $logfile_tail);
      @fclose ($fhandle);
      }
}
//===========================================================================

//===========================================================================
function MWX__file_exists_include_path ($filename)
{
   // Check for absolute path
   if (realpath($filename) == $filename)
      return true;

   // Otherwise, treat as relative path
   $paths = explode (PATH_SEPARATOR, get_include_path());
   foreach ($paths as $path)
      {
      if (file_exists (rtrim($path, '/') . '/' . ltrim($filename, '/')))
         return true;
      }

   return false;
}
//===========================================================================

//===========================================================================
function MWX__send_email ($email_to, $email_from, $subject, $plain_body)
{
//DANIEL//
   $mwx_settings = MWX__get_settings ();

   $use_smtp      = $mwx_settings['smtp_enabled']==1;
   $smtp_host     = $mwx_settings['smtp_host'];
   $smtp_username = $mwx_settings['smtp_username'];
   $smtp_password = $mwx_settings['smtp_password'];
   $smtp_port     = $mwx_settings['smtp_port'];
   $smtp_auth     = $mwx_settings['smtp_use_authentication'];

   $message = "
   <html>
   <head>
   <title>$subject</title>
   </head>
   <body>" . $plain_body . "
   </body>
   </html>
   ";

   // Strip tags feature.
   $message_text = preg_replace ('@<br[^>]*>@', "\n", $message);
   $message_text = trim(preg_replace ('@<[^>]+>@', " ",    $message_text));



   if ($use_smtp && (!MWX__file_exists_include_path("Mail.php") || !MWX__file_exists_include_path("Mail/mime.php")))
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: SMTP is enabled but no Pear libraries are installed. Mail.php and Mail/mime.php must exists for SMTP support to work. Trying to use mail()...");
      $use_smtp = false;
      }

   if ($use_smtp)
      {
      require_once ("Mail.php");
      require_once ("Mail/mime.php");

      $headers["From"]    = $email_from;
      $headers["To"]      = $email_to;
      $headers["Subject"] = $subject;


      $params["host"] = $smtp_host;
      $params["port"] = $smtp_port;
      $params["auth"] = $smtp_auth==1;
      $params["username"] = $smtp_username;
      $params["password"] = $smtp_password;

      $mime = new Mail_mime("\n");
      $mime->setTXTBody($message_text);
      $mime->setHTMLBody($message);

      $body = $mime->get();
      $headers = $mime->headers($headers);

      // Create the mail object using the Mail::factory method
      $mail_object =& Mail::factory("smtp", $params);
      $bRetCode = $mail_object->send($email_to, $headers, $body);
      if ($bRetCode == true)
         MWX__log_event (__FILE__, __LINE__, "Successfully sent SMTP email from: $email_from to: $email_to.");
      else
         MWX__log_event (__FILE__, __LINE__, "ERROR: SMTP mail send failed. Error sending email from: $email_from to: $email_to.");
      }
   else
      {
      // To send HTML mail, the Content-type header must be set
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      // Additional headers
      $headers .= "To: " . $email_to . "\r\n";        //"To: Mary <mary@example.com>, Kelly <kelly@example.com>" . "\r\n";
      $headers .= "From: " . $email_from . "\r\n";    //"From: Birthday Reminder <birthday@example.com>" . "\r\n";
                                       // $headers .= "Cc: birthdayarchive@example.com" . "\r\n";
                                       // $headers .= "Bcc: birthdaycheck@example.com" . "\r\n";
      // Mail it
      $bRetCode = @mail ($email_to, $subject, $message, $headers);
      if ($bRetCode)
        MWX__log_event (__FILE__, __LINE__, "Successfully sent email from: $email_from to: $email_to. (mail() returned true)");
      else
        MWX__log_event (__FILE__, __LINE__, "ERROR: mail() failed. Error sending email from: $email_from to: $email_to.");
      }
//DANIEL

/*
This is old code - before Daniel Waltrous SMTP addition

$message = "
   <html>
   <head>
   <title>$subject</title>
   </head>
   <body>" . $plain_body . "
   </body>
   </html>
   ";

   // To send HTML mail, the Content-type header must be set
   $headers  = 'MIME-Version: 1.0' . "\r\n";
   $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

   // Additional headers
   $headers .= "To: " . $email_to . "\r\n";        //"To: Mary <mary@example.com>, Kelly <kelly@example.com>" . "\r\n";
   $headers .= "From: " . $email_from . "\r\n";    //"From: Birthday Reminder <birthday@example.com>" . "\r\n";
                                                // $headers .= "Cc: birthdayarchive@example.com" . "\r\n";
                                                // $headers .= "Bcc: birthdaycheck@example.com" . "\r\n";
   // Mail it
   $bRetCode = mail ($email_to, $subject, $message, $headers);
   if ($bRetCode)
      MWX__log_event (__FILE__, __LINE__, "Successfully sent email from: $email_from to: $email_to. (mail() returned true)");
   else
      MWX__log_event (__FILE__, __LINE__, "ERROR: mail() failed. Error sending email from: $email_from to: $email_to.");
*/
}
//===========================================================================

//===========================================================================
// Save _GET, _POST, _SERVER, _COOKIE in new .html file.

function MWX__log_vars()
{
   $var1 = MWX__get_var ($_SERVER,    '$_SERVER');
   $var2 = MWX__get_var ($_GET,       '$_GET');
   $var3 = MWX__get_var ($_POST,      '$_POST');
   $var4 = MWX__get_var ($_COOKIE,    '$_COOKIE');

   $output =<<<OUTOUT
   <html>
       <body>
           $var3
           $var2
           $var1
           $var4
       </body>
   </html>
OUTOUT;

   // Save output into unique file
   $log_file_num   = 0;

   do
      {
      $log_file_num ++;
      $log_filename   = dirname(__FILE__) . "/__log_vars_$log_file_num.html";
      }
   while (@file_exists ($log_filename));

   $fhandle = fopen ($log_filename, 'w');
   if ($fhandle)
      {
      fwrite ($fhandle, $output);
      fclose ($fhandle);
      }

   return $output;
}

function MWX__get_var ($var, $varname)
{
   $style='"font:12px Verdana;color:blue;"';
   $output = MWX__output_varname ($varname);
   foreach ($var as $key => $value)
      {
      $output .= ("&nbsp;&nbsp;&nbsp;<span style=$style>$varname</span>" . '[\'' . MWX__output_key($key) . '\']=\'' . MWX__output_value($value) . "'");
      $output .= '<br />';
      }

   return $output;
}

function MWX__output_varname ($varname)
{
    $style='"font:14px Verdana bold;color:blue;"';
    return "<hr />" . "<p style=$style>$varname:</p>";
}
function MWX__output_key ($key)
{
    $style='"font:10px Verdana;color:green;"';
    return "<span style=$style>$key</span>";
}
function MWX__output_value ($value)
{
    $style='"font:10px Verdana;color:red;"';
    return "<span style=$style>$value</span>";
}
//===========================================================================

?>