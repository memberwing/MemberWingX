<?php

  if (basename($_SERVER['SCRIPT_FILENAME'])=='profile.php' && is_user_logged_in() && !MWX__is_user_admin())
    {
    $page_for_posts_id = get_option ('page_for_posts');
    $redirect_to = "";
    if ($page_for_posts_id)
    	$redirect_to = get_page_link ($page_for_posts_id);

    if (!$page_for_posts_id || !$redirect_to)
    	$redirect_to = get_bloginfo('url');

    // MWX__log_event (__FILE__, __LINE__, "About to be redirected to: " . $redirect_to);
    header ("HTTP/1.1 301 Moved Permanently");
    header ('Location: ' . $redirect_to);  // Redirect user

     // global $current_user;            // currently logged-on user
     // $user_id = $current_user->id;    // All user's data: $current_user_data = get_userdata ($current_user->id);

     // $products_purchased = MWX__GetListOfProductsForUser ($user_id);

     // if (is_array($products_purchased))
     //    {
     //    foreach ($products_purchased as $product)
     //       {
     //       if ($product['product_status'] != 'active')
     //          continue;   // Skip inactive products.

     //       //  stristr ($haystack, $needle)
     //       if (stristr ($product['product_name'], 'gold'))
     //          {
     //          header("HTTP/1.1 301 Moved Permanently");
     //          header('Location: ' . get_page_uri (get_option ('page_for_posts')));  // Redirect user
     //          }
     //       }
     //    }
    }
