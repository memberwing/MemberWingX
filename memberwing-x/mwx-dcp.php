<?php

/*
Digital Content Protection functionality
*/

// DCP encryption key
//{{{PHP_DO_NOT_ENCODE}}}
if (MWX__License_Allowed ())
   {
   include_once (dirname(__FILE__) . '/mwx-tracefusion.php');

   //---------------------------------------------------------------------------
   // Digital content protection (DCP) init sequence
   add_action ('init',                    'MWX__DCP_init');
   register_deactivation_hook(__FILE__,   'MWX__DCP_uninit');
   add_filter ('rewrite_rules_array',     'MWX__DCP_rewrite_rules_array',  1);
   add_filter ('query_vars',              'MWX__DCP_query_vars',           1);
   add_action ('parse_request',           'MWX__DCP_parse_request',        1);
   }
//---------------------------------------------------------------------------
//{{{/PHP_DO_NOT_ENCODE}}}


//===========================================================================
function MWX__DCP_init ()
{
   // Recreate rewrite rules
   /// These cause 404 Errors with custom post types with Wordpress 3.x
   /// global $wp_rewrite;
   /// $wp_rewrite->flush_rules();

   $mwx_settings = MWX__get_settings();

   // Install .htaccess into physical root location of premium files.
   MWX__DCP_WriteHtaccessFile ($mwx_settings['protected_files_physical_addr'], TRUE, FALSE);  // Create destination dir, but do not overwrite .htaccess if already exists.
}
//===========================================================================

//===========================================================================
function MWX__DCP_uninit ()
{
   // Recreate rewrite rules
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
}
//===========================================================================

//===========================================================================
//
//
function MWX__DCP_rewrite_rules_array ($rules)
{
   $mwx_settings = MWX__get_settings();
   if (isset($mwx_settings['protected_files_logical_addr']))
      $capture = trim($mwx_settings['protected_files_logical_addr'], '/ ');
   else
      return;

   $new_rules = array ($capture . '/(.+?)/?$' => 'index.php?get_premium_file=$matches[1]');
   $rules = array_merge ($new_rules, $rules);

   return ($rules);
}
//===========================================================================

//===========================================================================
function MWX__DCP_query_vars ($qvars)
{
   $qvars[] = 'get_premium_file';
   return $qvars;
}
//===========================================================================

//===========================================================================
//
// Main digital content protection goes on here.

function MWX__DCP_parse_request ($request)
{
   // Wordpress redirect syntax: wp_redirect ($full_web_url);

   if (!isset ($request->query_vars['get_premium_file']))
      return $request;  // Not our request.

   $requested_premium_object = $request->query_vars['get_premium_file'];
   if (!$requested_premium_object)
      {
      $request->query_vars['error'] = '404';
      return $request;
      }

   // Pull MemberWing settings
   $mwx_settings = MWX__get_settings();

   if (!isset($mwx_settings['protected_files_physical_addr']))
      return $request;  // Option is not set - nowhere to redirect

   $phys_addr = rtrim($mwx_settings['protected_files_physical_addr'], '/ ');

   // Detected 'get_premium_file' in query string.
   //    /files/DIR/SECRET.JPG -> index.php?get_premium_file=DIR/SECRET.JPG
   //
   // Pulling file:
   //    $mwx_settings['protected_files_physical_addr'] / DIR/SECRET.JPG

   // Construct full physical objectname
   $full_obj_name = $phys_addr . '/' . $requested_premium_object; // Get physical address of site root

   $serving_premium = TRUE;

   if (!MWX__DCP_User_Allowed_To_Access_Object ($requested_premium_object, $mwx_settings))
      {
      // Unauthorized user detected who is trying to access premium content.
      //
      $serving_premium = FALSE;
      // User is not authorized to view image
      // Append '_denied' to filename before extension.
      ///!!! NOTE: THIS REGEX IS INVALID, CHECKIT FOR 'a/b/hi' file
      ///!!! NOTE: THIS MUST BE MOVED TO SEPARATE FUNCTION
      $denied_obj_name = preg_replace ('|/([^\./]+)([^/]+)$|', "/$1_denied$2", $full_obj_name);

      if (file_exists($denied_obj_name))
         $full_obj_name = $denied_obj_name;
      else
         {
         // Replace filename before extension to 'denied'.
         $denied_obj_name = preg_replace ('|/[^\./]+([^/]+)$|', "/denied$1", $full_obj_name);

         if (file_exists($denied_obj_name))
            $full_obj_name = $denied_obj_name;
         else
            {
            // Last attempt: try to get 'denied'.ext from the root of PREMIUM_FILES folder.
            // Replace requested object with just 'denied.ext'
            $full_obj_name = $phys_addr . preg_replace ('|.*/[^\./]+([^/]+)$|', "/denied$1", $requested_premium_object);
            }
         }
      }

   // TraceFusion content protection is active when serving premium file + option is set in admin panel.
   $tracefusion_protection = $serving_premium && $mwx_settings['tracefusion_tracing_enabled'];

   if (!file_exists($full_obj_name))
      {
/////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!
///MWX__log_event (__FILE__, __LINE__, "=== Mark: fon=$full_obj_name");
/////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!
      $request->query_vars['error'] = '404';
      return $request;
      }

/////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!
///MWX__log_event (__FILE__, __LINE__, "=== Mark: fon=$full_obj_name");
/////////////////////////!!!!!!!!!!!!!!!!!!!!!!!!!!

   $obj_mime_type = MWX__get_mime_type ($full_obj_name);   // 'text/plain'

   ob_start();

// Set encoding
//{{{PHP_DO_NOT_ENCODE}}}
   header ("Content-Type: $obj_mime_type");
   header ("Last-Modified: " . gmdate ("D, d M Y H:i:s", @filemtime($full_obj_name))." GMT");
   if (!$serving_premium)
      {
      header ("Cache-Control: no-store, no-cache");
      header ("Cache-Control: must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
      header ("Pragma: no-cache");
      header ("Expires: Mon, 11 Jan 2000 12:34:56 GMT");   // Date in the past
      }

   // Make sure file type is not plaintext (.RTF is ok) where tracefusion signature cannot be applied to.
   if (strpos ($obj_mime_type, 'text/') === 0 && ($obj_mime_type != 'text/rtf'))
      $file_is_plain_text = TRUE;
   else
      $file_is_plain_text = FALSE;

   if ($tracefusion_protection && !$file_is_plain_text)
      {
      //------------------------------------
      // Service premium contents here. Assemble TraceFusion signature
      // ...
      global   $current_user;
      wp_get_current_user();

      $tracefusion_data = array (
         'software_info'      => array (
            'software'     => 'MemberWing-X',
            'edition'      => MEMBERWING_X_EDITION,
            'version'      => MEMBERWING_X_VERSION,
            'license_key'  => $mwx_settings['mwx_api_key'], // Request license key must match this key for results to be disclosed.
            ),
         'owner_info'     => array (
            'sitename'     => $_SERVER['HTTP_HOST'],
            'tracefusion_key'  => $mwx_settings['mwx_api_key'], // Request license key must match this key for results to be disclosed.
            'custom'       => '',
            ),
         'file'               => array (                             // Information about file requested. WEB URL / physical filesystem name
            'name_url'     => 'http' . (isset($_SERVER['HTTPS'])?"s":"") . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'name_system'  => $full_obj_name,
            ),
         'user_info'          => array (
            'datetime'     => date ("Y-m-d H:i:s T", time()),   // Datetime of request
            'ip'           => $_SERVER['REMOTE_ADDR'],               // IP address of requestor
            'user_agent'   => (string)@$_SERVER['HTTP_USER_AGENT'],
            'referer'      => (string)@$_SERVER['HTTP_REFERER'],              // Name of page where request came from.
            'server_remote_user' => isset($_SERVER['REMOTE_USER'])?$_SERVER['REMOTE_USER']:"",  // Authenticated username
            'login'        => $current_user->user_login,
            'firstname'    => $current_user->user_firstname,
            'lastname'     => $current_user->user_lastname,
            'email'        => $current_user->user_email,
            ),
         'system'             => array (
            'software'     => 'TraceFusion',
            'author'       => 'Gleb Esman, MemberWing-X, http://www.memberwing.com/',
            'contact'      => 'gleb@memberwing.com',
            ),
         );

      // Use API key as encryption key.
      $tracefusion_signature = MWX__TraceFusion_PrepareTraceFusionSignature ($tracefusion_data, $mwx_settings['mwx_api_key']);
      //------------------------------------

      header ("Content-Length: " . (@filesize ($full_obj_name) + strlen($tracefusion_signature)));
      }
   else
      {
      header ("Content-Length: " . @filesize ($full_obj_name));
      }

   header ("Content-Transfer-Encoding: binary");

//{{{/PHP_DO_NOT_ENCODE}}}

   if (!ini_get ('safe_mode'))
      set_time_limit (0);

   // php.net contributer's notes:
   // To anyone that's had problems with Readfile() reading large files into memory the problem is not Readfile() itself,
   // it's because you have output buffering on. Just turn off output buffering immediately before the call to Readfile(). Use something like ob_end_flush().
   @ob_end_flush ();

   @readfile ($full_obj_name);

   if ($tracefusion_protection)
      echo $tracefusion_signature;

   // This might not work for huge files:
   //    $file_data = @file_get_contents ($full_obj_name);
   //    echo $file_data;

   exit();
}
//===========================================================================

//===========================================================================
//
// $obj_relative_name - non pre-slashed name of object relative to 'protected_files_physical_addr'. Ex:
//    gold/projects.pdf
//

function MWX__DCP_User_Allowed_To_Access_Object ($obj_relative_name, $mwx_settings=FALSE)
{
   global $current_user;            // currently logged-on user
   $user_id = @$current_user->ID;    // All user's data: $current_user_data = get_userdata ($current_user->ID);

   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings();

   if (MWX__DCP_File_is_Locked ($obj_relative_name, $mwx_settings))
      return (FALSE);

   if (MWX__DCP_File_is_Unrestricted ($obj_relative_name, $mwx_settings))
      return (TRUE);

   if ($user_id < 1 || (MWX__is_user_admin() && $mwx_settings['admin_acts_like_regular_visitor']))
      {
      return FALSE;  // Non logged on visitor (or admin acting like visitor) cannot see any premium post/page at any time.
      }

   if (MWX__is_user_admin())
      return TRUE;   // Admin acts like admin

   $dir_names = explode ('/', $obj_relative_name);
   $just_filename = array_pop ($dir_names);   // Pull and remove last element (filename) from array of names
   $obj_relative_dirname = implode ('/', $dir_names); // "gold/files"

   // Remove extension, leaving dot in place.
   $pathname_without_ext = preg_replace ('|(\.)[^\.]+$|', "$1", $obj_relative_name);

   // See if object is inside of any 'free' directory. Which means any logged on user allowed to access it.
   // ./free/file.bin
   // ./free/gfd/file.bin
   // ./asd/free/gfd/file.bin
   if (in_array ('free', $dir_names))
      return TRUE;

   // Here we know that file is not "free" file.

   // Pull these settings now.
   $dirnames_mwx_indiv_access   = explode (',', trim($mwx_settings['individual_access_directory_names']));
   $dirnames_mwx_group_access   = explode (',', trim($mwx_settings['group_access_directory_names']));

   //---------------------------------------
   // Construct only group access names from all dirnames preceding filename:
   // /this/is/membership/gold/yes/file.bin -> array ('membership', 'gold');

   $dir_names_group_access = array ();
   foreach ($dir_names as $dirname)
      {
      if (in_array ($dirname, $dirnames_mwx_group_access))
         $dir_names_group_access[] = $dirname;
      }
   //---------------------------------------

   //---------------------------------------
   // Check if this file is "individual access" file (contained under one or more 'individual access' dirnames)
   $file_is_indiv_access = FALSE;
   foreach ($dir_names as $dirname)
      {
      if ($dirname[0] == '_' || $dirname[0] == '.' || in_array ($dirname, $dirnames_mwx_indiv_access))
         {
         $file_is_indiv_access = TRUE;
         break;
         }
      }
   //---------------------------------------

   // $products_purchased is: array of purchased items:
   // array (array('product_id'=>'5', 'product_name'=>'', 'date'=>'2009-12-02', 'txn_id'=>array(...), 'subscr_id'=>'', 'active'=>'1'), array(...), ...)
   $products_purchased = MWX__GetListOfProductsForUser ($user_id);

   if (is_array($products_purchased))
      {
      foreach ($products_purchased as $product)
         {
         if (!MWX__is_product_active($product['product_status']))
            continue;   // Skip inactive products.

         if ($file_is_indiv_access)
            {
            // Match Individual access file.

            // See if user owns this specific file: search for "dir/path/file-name-without-ext." inside item name
            if (stristr ($product['product_name'], $pathname_without_ext))
               return TRUE;

            // See if user owns a product that allows certain filerange/filespec, and match it against this file
            //    [filespec-daterange-1: downloads/racing:+1 month]
            if (MWX__FilespecDaterangeMatch ($product, $obj_relative_dirname, $just_filename, $mwx_settings))
               return TRUE;
            }
         else if (count($dir_names_group_access))
            {
            // See if user owns item whose name includes every group access dir in $dir_names array.
            // Match group access dirs against product name

            // See if user owns a product whose name contains *all* group access dirnames preceding this filename. Ex:
            // /gold/file.bin - use must own "Something Like Gold" product.
            // /membership/gold/file.bin - use must own "Something Like Gold and Great Membership" product.

            $matches=0;
            foreach ($dir_names_group_access as $dirname_r)
               {
               if (stristr ($product['product_name'], $dirname_r))
                  $matches++;
               else
                  break;
               }

            if ($matches == count ($dir_names_group_access))
               return TRUE;   // Found product that contains all group access dirnames
            }
         else
            {
            // Object is not individual access and is not under group access name -
            // - meaning that if user owns anything at all - he is allowed to access this file
            return TRUE;
            }
         }
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
// TRUE: file is located under unrestricted tree

function MWX__DCP_File_is_Locked ($obj_relative_name, $mwx_settings)
{
   // array ('UPLOADS', 'temporary', ); ...
   $dirnames_mwx_locked_access   = explode (',', trim($mwx_settings['locked_access_directory_names']));

   $dir_names = explode ('/', $obj_relative_name);
   array_pop ($dir_names);   // Pull and remove last element (filename) from array of names

   foreach ($dir_names as $dirname)
      {
      if (!$dirname)
         continue; // Skip possible 'dir//dir' or '/dir/dir2'.
      if (in_array ($dirname, $dirnames_mwx_locked_access))
         return TRUE;
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
// TRUE: file is located under unrestricted tree

function MWX__DCP_File_is_Unrestricted ($obj_relative_name, $mwx_settings)
{
   // array ('img', 'css', 'js', ); ...
   $dirnames_mwx_unrestricted_access   = explode (',', trim($mwx_settings['unrestricted_access_directory_names']));

   $dir_names = explode ('/', $obj_relative_name);
   array_pop ($dir_names);   // Pull and remove last element (filename) from array of names

   foreach ($dir_names as $dirname)
      {
      if (!$dirname)
         continue; // Skip possible 'dir//dir' or '/dir/dir2'.
      if (in_array ($dirname, $dirnames_mwx_unrestricted_access))
         return TRUE;
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
//
// '$overwrite_file' = TRUE => overwrite destination file, if exists.

function MWX__DCP_WriteHtaccessFile ($destination_dir, $create_destination_dir=TRUE, $overwrite_file=FALSE)
{
   $htaccess_filename = rtrim ($destination_dir, '/ ') . '/.htaccess';

   if ($create_destination_dir)
      $RetCode = @mkdir ($destination_dir, 0777, true);

   if (@file_exists($htaccess_filename) && !$overwrite_file)
      return;

   @copy (dirname(__FILE__) . '/PREMIUM.htaccess', $htaccess_filename);
}
//===========================================================================

//===========================================================================
// Match file location and filename to possible filespec defined in product name.
// Returns: TRUE if product_name is indeed filespec and file is indeed matches spec definition
//
// $product_name could be:
//
//    +==================+================+==========+========+===============+
//    | type of filespec |location covered|start date|duration|Extra attrs(op)|
//    +==================+================+==========+========+===============+
//    [      fs-daterange:downloads/racing:today     :+2 days :               ]   -  total 3 days, today + 2 more days
//    [      fs-daterange:downloads/racing:today     :-2 weeks:               ]   -  previous 2 weeks, up to but not including today
//    [      fs-daterange:downloads/racing:today     :=10 days:               ]   -  total 10 days, including today.
//    [      fs-daterange:downloads/racing:today     :+7 days :s,ft           ]   -  total 8 days, including today AND including subdirs AND using filetime for date detection (default:use filename)
//    [      fs-daterange:downloads/racing:tomorrow  :+7 days :s,ft           ]   -  total 8 days, starting from tomorrow AND including subdirs AND using filetime for date detection (default:use filename)
//    [      fs-daterange:downloads/racing:2010-12-03:=1 year :               ]   -  total 1 year, starting from and including 2010-12-03
//    [      fs-daterange:downloads/racing:2010-12-03:2010-12-31:             ]   -  from 2010-12-03 till 2010-12-31, all inclusive
//
//    Extra attributes (comma separated):
//    s  - including subdirectories. Default: not including
//    ft - use filetime for date scanning. If not specified - filename will be used for date detection (...2010-12-21.pdf)


function MWX__FilespecDaterangeMatch ($product_arr, $obj_relative_dirname, $obj_just_filename, $mwx_settings)
{
   $name_offset = strrpos ($product_arr['product_name'], '[');
   if ($name_offset === FALSE)
      return FALSE;  // Name is not spec.

   $prod_spec = explode (':', substr($product_arr['product_name'], $name_offset));
   if (count($prod_spec) < 4)
      return FALSE;  // Product is not daterange spec

   // Normalize product spec
   foreach ($prod_spec as $k=>$v)
      $prod_spec[$k] = trim ($v, " []");

   if ($prod_spec[0] != 'fs-daterange')
      return FALSE;

   //---------------------------------------
   // Gather extra attributes if present and set defaults
   if (isset($prod_spec[4]))
      $prod_spec_attrs = explode (',', $prod_spec[4]);
   else
      $prod_spec_attrs = array ();

   $ATTR_include_subdirs         =  in_array ('s',  $prod_spec_attrs);
   $ATTR_use_filename_for_date   = !in_array ('ft', $prod_spec_attrs);
   //---------------------------------------

   if ($ATTR_use_filename_for_date)
      {
      // Search for date spec anywhere in filename
      if (!preg_match ('|\d\d\d\d\-\d\d\-\d\d|', $obj_just_filename, $matches))
         return FALSE;

      $file_date_unix = strtotime ($matches[0]);   // Filedate rounded to date only (no minute-precision)
      }
   else
      {
      // Get last modified date of file.
      $file_date_unix = filectime ($mwx_settings['protected_files_physical_addr'] . "/" . $obj_relative_dirname . "/" . $obj_just_filename);

      // Re-normalize file datetime to file date only.
      $file_date_unix = strtotime (date ("Y-m-d", $file_date_unix));
      }

   // non-slashed dirname after PREMIUM_FILES, such as 'gold/files'
   $spec_relative_path = $prod_spec[1];

   // Is file inside the dir tree that this spec applies to?
   if ($ATTR_include_subdirs)
      {
      // $obj_relative_dirname must begin with $spec_relative_path
      if (strpos ($obj_relative_dirname, $spec_relative_path) !== 0)
         return FALSE;  // It is spec but this file is not under qualified tree.
      }
   else
      {
      if ($obj_relative_dirname != $spec_relative_path)
         return FALSE;  // It is spec but this file is not under qualified tree.
      }


   //---------------------------------------
   // Assemble range of dates for this spec.
   // Starting date...
   if ($prod_spec[2] == 'today')
      {
      $start_date       = substr ($product_arr['purchase_date'], 0, 10);   // Extract purchase date, date only (no time needed).
      $start_date_unix  = strtotime ($start_date);
      }
   else if ($prod_spec[2] == 'tomorrow')
      {
      $start_date       = substr ($product_arr['purchase_date'], 0, 10);   // Extract purchase date, date only (no time needed).
      $start_date_unix  = strtotime ($start_date . ' + 1 day');
      }
   else
      {
      $start_date       = $prod_spec[2];
      $start_date_unix  = strtotime ($start_date);
      if ($start_date_unix === FALSE)
         return FALSE;  // Invalid value set in 'start date' section
      }

   // Ending date
   if (preg_match ('|\d\d\d\d\-\d\d\-\d\d|', $prod_spec[3], $matches))
      {
      // Ending date was specified in exact format: 2010-12-31
      $end_date_unix = strtotime ($matches[0]);
      }
   else
      {
      // Ending date was specified in mixed formats: -/+/= ...
      $seconds_correction = 0;

      // Parse duration
      // Assemble textual end_date first.
      if ($prod_spec[3][0] == '+')
         {
         $end_date = $start_date . " " . $prod_spec[3];
         }
      else if ($prod_spec[3][0] == '-')
         {
         $start_date_unix -=1;   // NEVER including starting date, if spec starts with '-'
         $end_date = $start_date . " " . $prod_spec[3];
         }
      else if ($prod_spec[3][0] == '=')
         {
         $end_date = $start_date . " +" . substr($prod_spec[3], 1);  // '=' replaced with '+' and seconds correction set to -1.
         $seconds_correction = -1;
         }
      else
         {
         // return FALSE; // some sort of invalid char specified at the beginning of duration field.

         // Assume '=' behavior here.
         $end_date = $start_date . " +" . $prod_spec[3];  // '=' replaced with '+' and seconds correction set to -1.
         $seconds_correction = -1;
         }

      $end_date_unix = strtotime ($end_date) + $seconds_correction;
      }
   //---------------------------------------


   // Final comparison
   if ($file_date_unix >= min($start_date_unix,$end_date_unix) && $file_date_unix <= max($start_date_unix,$end_date_unix))
      return TRUE;

   return FALSE;

}
//===========================================================================


?>