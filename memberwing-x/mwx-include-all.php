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
   require_once ($g_blog_dir . '/wp-admin/includes/admin.php');
   }
//------------------------------------------

require_once (ABSPATH . WPINC . '/registration.php');          // Access to email_exists()

require_once (dirname(__FILE__) . '/mwx-admin.php');

if (!function_exists('MWGC_where') && get_bloginfo('version') >= 2.8)
   {
   // Include gradual content delivery code only if MemberWing Legacy is not installed and we are running version 2.8+
   include_once (dirname(__FILE__) . '/mwx-gradual-content.php');
   }

?>