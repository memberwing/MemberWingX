<?php
/*
   MemberWing-X Digital Online Store Builder API Interface with third party services and applications
   ==================================================================================================
   Syntax for GET/POST request:

   POST URL (your full URL to mwx-dos-api.php):
      http://WWW.YOUR-SITE.COM/wp-content/plugins/memberwing-x/dos-widget/mwx-dos-api.php

   Input GET/POST Params:
      'format'       -  'html' (default), 'json'.
      'use_template' -  optional: template name ot use. 't1' is default.
      'regex_include'-  Empty => all inclusive.    Evaluated first.                 Complete pattern, such as: '@\.pdf$@i'
      'regex_exclude'-  Empty => nothing excluded. Evaluated after include regex

      'max_items'    -  num of items to return. If unspecified, 0, FALSE or -1 => unlimited items

      'sort'         -  sort by...
      'test'         -  If specified - wraps everything in proper HTML page. Useful for testing of store templates: http://WWW.YOUR-SITE.COM/wp-content/plugins/memberwing-x/dos-widget/mwx-dos-api.php?test
*/


/// This piece was present to compensate for 404 header set by wordpress for all non-WP pages.
/// Since 6.020 mwx-include-all.php was updated to force-add header "200" and this no longer necessary.
///if (0 && !isset($_GET['api_call_in_progress']))
///{
///   // We need to call ourselves via curl to suppress invalid 404 header set by include('mwx-include-all.php') called by bare script like us.
///   // This invalid header cause jquery .ajax() c all to silently fail without success or error handlers callbacks called.
///   // It needs to be figured out why 404 is happening though. Likely because of issue with manual "load wordress" logic.
///
///   // Autodetect our own WEB URL
///   $url = 'http' . (isset($_SERVER['HTTPS'])?"s":"") . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
///   $url .= '&api_call_in_progress=1';
///
///   $options = array(
///      CURLOPT_URL            => $url,
///      CURLOPT_RETURNTRANSFER => true,     // return web page
///      CURLOPT_HEADER         => false,    // don't return headers
///      CURLOPT_FOLLOWLOCATION => true,     // follow redirects
///      CURLOPT_CONNECTTIMEOUT => 60,       // timeout on connect
///      CURLOPT_TIMEOUT        => 60,       // timeout on response in seconds.
///      CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
///      );
///
///   $ch      = curl_init    ();
///   curl_setopt_array       ($ch, $options);
///   $ret_data = curl_exec   ($ch);
///   // $err     = curl_errno  ($ch);
///   // $header  = curl_getinfo($ch);
///   // $errmsg  = curl_error  ($ch);
///   curl_close              ($ch);
///
///   // Please note that $header['http_code']==404 here, even though the $ret_data is valid.
///
///   $content_type = 'Content-type: text/javascript; charset: UTF-8';
///   header ($content_type);
///   echo $ret_data;   // Now we're talking...
///   exit;
///}

ob_start(); // Suppress possible output accidents
// Load wordpress / MemberWing-X
include_once (dirname(__FILE__) . '/../mwx-include-all.php');
ob_end_clean();

$_INPUT_REQUEST = array_merge ((isset($_GET)&&is_array($_GET))?$_GET:array(), (isset($_POST)&&is_array($_POST))?$_POST:array());

// Decode inputs
foreach ($_INPUT_REQUEST as $key => $value)
   {
   $_INPUT_REQUEST[$key] = urldecode(stripslashes($value));
   }

$dos_output = MWX__digital_online_store ($_INPUT_REQUEST);

//-------------------------------------------------------------
// Embedding template's stylesheet and javascript
$mwx_settings     = MWX__get_settings ();
$current_template = $mwx_settings['dos_current_active_template']; // Default: 't1'. But this value is == last edited template in admin screen. Allows admin screen persistance
$url_css          = $mwx_settings['dos_templates'][$current_template]['dos_style_stylesheet_file'];
$url_js           = $mwx_settings['dos_templates'][$current_template]['dos_style_javascript_file'];

$embed_js_css=<<<TTT
<script type="text/javascript">
   // Add widget's .css
   var headID = document.getElementsByTagName("head")[0];
   var cssNode = document.createElement('link');
   cssNode.type = 'text/css';
   cssNode.rel = 'stylesheet';
   cssNode.href = '$url_css';
   cssNode.media = 'screen';
   headID.appendChild(cssNode);

   // Force load jquery first
   (function()
      {
      // Load jQuery if not present
      if (window.jQuery === undefined)
         {
         var script_tag = document.createElement('script');
         script_tag.setAttribute("type",  "text/javascript");
         script_tag.setAttribute("src",   "https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
         script_tag.onload = scriptLoadHandler;
         script_tag.onreadystatechange = function ()
            { // Same thing but for IE
            if (this.readyState == 'complete' || this.readyState == 'loaded')
               {
               scriptLoadHandler();
               }
            };

         // Try to find the head, otherwise default to the documentElement
         (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
         }
      else
         {
         // The jQuery version on the window is the one we want to use
         jQuery = window.jQuery;
         main();
         }

      // Called once jQuery has loaded
      function scriptLoadHandler()
         {
         // Restore $ and window.jQuery to their previous values and store the
         // new jQuery in our local jQuery variable
         jQuery = window.jQuery.noConflict(true);

         // Call our main function
         main();
         }

      // main function
      function main()
         {
         jQuery(document).ready(function($)
            {
            // Load template's .js (that relies on jQuery to be already available)
            $.getScript ('$url_js');
            });
         }
   })(); // We call our anonymous function immediately
</script>
TTT;
//-------------------------------------------------------------

$dos_output = $embed_js_css . $dos_output;

//-------------------------------------------------------------
// If 'test' - wrap everything in proper single HTML page just for test/demo purposes
if (isset($_INPUT_REQUEST['test']))
   {
$dos_output=<<<TTT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Digital Online Store - Powered by MemberWing-X</title>
   </head>
   <body>
      <div align="center">
         $dos_output
      </div>
   </body>
</html>
TTT;
   }
//-------------------------------------------------------------

// Pack output depending on format requested.
if ($_INPUT_REQUEST['format'] == 'json' || @$_INPUT_REQUEST['jsoncallback'])
   {
   $content_type = 'Content-type: text/javascript; charset: UTF-8';

   // JSON wrap.
   if (@$_INPUT_REQUEST['jsoncallback'])
      $jsoncallback = $_INPUT_REQUEST['jsoncallback'];
   else
      $jsoncallback = FALSE;

   if ($jsoncallback)
      $dos_output = $jsoncallback . "(" . json_encode (array('html' => $dos_output)) . ")";
   else
      $dos_output = json_encode($dos_output);
   }
else
   {
   // Assuming 'html'
   $content_type = 'Content-type: text/html; charset: UTF-8';
   }

header ($content_type);

echo $dos_output;

?>