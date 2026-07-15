<?php

require_once (dirname(__FILE__) . '/mwx-utils.php');
require_once (dirname(__FILE__) . '/mwx-notify-utils.php');
require_once (dirname(__FILE__) . '/mwx-aff-utils.php');

//------------------------------------------
// Load wordpress
if (!defined('WP_USE_THEMES') && !defined('ABSPATH'))
   {
   $g_blog_dir = preg_replace ('|(/+[^/]+){4}$|', '', str_replace ('\\', '/', __FILE__)); // For love of the art of regex-ing
   define('WP_USE_THEMES', false);
   require_once ($g_blog_dir . '/wp-blog-header.php');

   // Force-elimination of header 404 for non-wordpress pages.
   header ("HTTP/1.1 200 OK");
   header ("Status: 200 OK");

   require_once ($g_blog_dir . '/wp-admin/includes/admin.php');
   }
//------------------------------------------

// Note: wp-includes/registration.php and class-phpass.php includes removed -
// email_exists() and wp_check_password() are always available in modern WordPress.

require_once (dirname(__FILE__) . '/mwx-admin.php');
require_once (dirname(__FILE__) . '/mwx-dcp.php');
require_once (dirname(__FILE__) . '/mwx-fcf.php');
require_once (dirname(__FILE__) . '/mwx-dos.php');
require_once (dirname(__FILE__) . '/mwx-dos-admin.php');

if (!function_exists('MWGC_where') && get_bloginfo('version') >= 2.8)
   {
   // Include gradual content delivery code only if MemberWing Legacy is not installed and we are running version 2.8+
   include_once (dirname(__FILE__) . '/mwx-gradual-content.php');
   }

if (file_exists(dirname(__FILE__) . '/mwx-beta-extras.php'))
   require_once (dirname(__FILE__) . '/mwx-beta-extras.php');

?>