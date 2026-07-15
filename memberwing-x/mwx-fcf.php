<?php
/*
MemberWing-X
-  Support for Google First Click Free Logic:
   http://googlewebmastercentral.blogspot.com/2008/10/first-click-free-for-web-search.html
*/

add_action                 ('init', 'MWX__FCF_init', 4);  // Buddupress uses '5' or default. Trying to be first.

//===========================================================================
//
// Sets special MemberWing-X first click free cookie.
// Current issue: new page, regardless if it is premium or free - sets cookie. Better would be to set cookie only for premium page visits.
// but at this point i don't know if given page is free or premium

function MWX__FCF_init ()
{
   $mwx_settings = MWX__get_settings ();
   if ($mwx_settings['first_click_free_enabled'])
      {
      if (MWX__visit_is_search_engine_spider())
         return;  // For spiders we don't set cookies.

      $normalized_request_uri = trim ($_SERVER['REQUEST_URI'], '/');    // With '/' stripped.

      if (preg_match ('|\..{2,4}$|', $normalized_request_uri))
         return;  // For non-essential pages we don't set cookies.

      // Visiting valid page here. We still don't know here though whether it is "premium" or "free" page.

      $visit_from_search_engine = MWX__visit_from_search_engine();

      $raw_cookies_array = isset($_COOKIE['memberwing-mwx-fcf'])?$_COOKIE['memberwing-mwx-fcf']:"";

      $new_cookie = ($visit_from_search_engine?"se:":"nn:") . MWX__FCF_current_page_hash() . ':xx';  // 'REQUEST_URI' includes query string - required for non-seo-optimized permalinks.

      // Overwrite cookie only if current visit is *not* from this blog, AND:
      //    -  cookie is empty   OR
      //    -  current visit is from search engine   OR
      //    -  it was recorded from previous visit from search engine
      if ($visit_from_search_engine)
         {
         // Force-insert new cookie into flat array.
         $new_cookies_array = MWX__FCF_add_fcf_cookie_to_raw_array ($mwx_settings, $raw_cookies_array, $new_cookie, FALSE);
         }
//      else if (MEETINN)   // Case for non-search engine referrers, including ourselves.
//         {
//         // Insert new cookie into array if it has empty or "se:"-type of slots available.
//         $new_cookies_array = MWX__FCF_add_fcf_cookie_to_raw_array ($raw_cookies_array, $new_cookie, false);
//         }
      else
         $new_cookies_array = "";

      if ($new_cookies_array && $new_cookies_array != $raw_cookies_array)
         {
         // If changes to cookie was made
         @setcookie ("memberwing-mwx-fcf", $new_cookies_array, strtotime("+5 years"), SITECOOKIEPATH);
         $_COOKIE['memberwing-mwx-fcf'] = $new_cookies_array;  // Save it so it will be available for the same page code.
         }

      }
}
//===========================================================================

//===========================================================================
function MWX__FCF_current_page_is_first_click_free ($mwx_settings=FALSE)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

   if ($mwx_settings['first_click_free_enabled'])
      {
      if (MWX__visit_is_search_engine_spider())
         return TRUE;

      $raw_cookies_array = isset($_COOKIE['memberwing-mwx-fcf'])?$_COOKIE['memberwing-mwx-fcf']:"";

      $free_page_hash_array = preg_replace ('#:..(\||$)#', "|", $raw_cookies_array);
      $free_page_hash_array = preg_replace ('#(^|\|)..:#', "|",   $free_page_hash_array);
      $free_page_hash_array = explode ('|', $free_page_hash_array);

      if (in_array (MWX__FCF_current_page_hash(), $free_page_hash_array))
         return TRUE;
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
//
// $raw_cookies_array = 'nn:5298573295:xx|nn:7421545135:xx|se:091234124:xx'
// $new_cookie        = 'se:7865634343:xx'
// $force_add:
//    FALSE - add cookie to array only if there are empty spots or "se:" types of cookies slots to replace
//    TRUE  - search for empty or "se:" types of slots first, if not found - replace oldest cookie with this one.
//
function MWX__FCF_add_fcf_cookie_to_raw_array ($mwx_settings, $raw_cookies_array, $new_cookie, $force_add)
{
   $required_arr_size = $mwx_settings['first_click_free_clicks_allowed'];
   $cookies_array = explode ('|', $raw_cookies_array);

   // Already in array? Remove it. It then will be added to the end of array (refreshed).
   if (in_array ($new_cookie, $cookies_array))
      {
      unset ($cookies_array[array_search($new_cookie, $cookies_array)]);
      }

   // Make sure cookies array matches admin option's pages number.
   if ($required_arr_size > count($cookies_array))
      {
      // Pad array with empty slots
      $cookies_array = array_pad ($cookies_array, $required_arr_size, "");
      }
   else if ($required_arr_size < count($cookies_array))
      {
      // Remove oldest cookies
      $num = count($cookies_array) - $required_arr_size;
      for ($i=0; $i<$num; $i++)
         array_shift ($cookies_array);
      }

   // Add cookie to array now
   // Try empty slots first.
   $slot_found = false;
   if (in_array ("", $cookies_array))
      {
      $cookies_array[array_search("", $cookies_array)] = $new_cookie;
      $slot_found = true;
      }
/*
PHP 5.x only. For MEETINNOVATORS
   else
      {
      // Try "se:" cookies second
      foreach ($cookies_array as &$cookie)
         {
         if (!strncmp ("se:", $cookie, 3))
            {
            $cookie = $new_cookie;
            $slot_found = true;
            break;
            }
         }
      }
*/
   if (!$slot_found && $force_add)
      {
      // Forcefully add new cookie to the end of array, losing the oldest cookie out.
      array_push ($cookies_array, $new_cookie);
      array_shift ($cookies_array);
      }

   $new_raw_cookie_array = implode ('|', $cookies_array);

   return ($new_raw_cookie_array);
}
//===========================================================================

//===========================================================================
function MWX__FCF_current_page_hash ()
{
      return substr(md5(trim ($_SERVER['REQUEST_URI'], '/')), -16);
}
//===========================================================================


?>