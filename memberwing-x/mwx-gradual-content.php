<?php

//---------------------------------------------------------------------------
add_filter  ('posts_where',            'MWX__where',              222);
add_filter  ('posts_join',             'MWX__join',               222);
add_filter  ('posts_request',          'MWX__full_request',       222);
add_filter  ('wp_list_categories',     'MWX__wp_list_categories', 222);    // Hide number of posts under each category. Instead of: "News (12)" will show: "News"

add_action  ('widgets_init',           'MWX__load_widgets'           );    // Add function to widgets_init that'll load our widget.
//---------------------------------------------------------------------------


//===========================================================================
function MWX__where ($where)
{
   global $wp_query, $wpdb;

   if (current_user_can ('edit_users'))
      return $where;

   $user_maturity = MWX__get_current_user_maturity ();

   return $where . " AND (meta_value IS NULL OR meta_value <= $user_maturity)";
}
//===========================================================================

//===========================================================================
//
// Invite metadata into consideration

function MWX__join  ($join)
{
   global $wpdb;

   if (current_user_can ('edit_users'))
      return $join;

   return $join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = 'maturity'";
}
//===========================================================================

//===========================================================================
//
// Eliminate duplicates

function MWX__full_request ($query)
{
   if (current_user_can ('edit_users'))
      return $query;

   if (strpos($query, 'DISTINCT') === FALSE)
      $query = str_replace('SELECT', 'SELECT DISTINCT', $query);

   return $query;
}
//===========================================================================

//===========================================================================
//
// Eliminate 'number of posts under category' as it may be misleading for ones who actually see
// less posts available:
// Before:
//    <a href="http://www.ttt.com/?cat=3" title="View all posts filed under My Posts">My Posts</a> (4)
// After:
//    <a href="http://www.ttt.com/?cat=3" title="View all posts filed under My Posts">My Posts</a>

function MWX__wp_list_categories ($param)
{
   if (current_user_can ('edit_users'))
      return $param;

   return preg_replace ('|(\</a\>)(\s*\(\d+\))|i', "$1", $param);
}
//===========================================================================




//===========================================================================
//
// Retrieve a list of pages while taking into account a user's maturity in days
// This is a modified version of get_pages() found in wp-includes/post.php
//
// * Retrieve a list of pages.
//
// The defaults that can be overridden are the following: 'child_of',
// 'sort_order', 'sort_column', 'post_title', 'hierarchical', 'exclude',
// 'include', 'meta_key', 'meta_value','authors', 'number', and 'offset'.
//
// @uses $wpdb
//
// @param mixed $args Optional. Array or string of options that overrides defaults.
// @return array List of pages matching defaults or $args
//
function &MWX__get_pages($args = '') {
   global $wpdb;

   $defaults = array(
      'child_of' => 0, 'sort_order' => 'ASC',
      'sort_column' => 'post_title', 'hierarchical' => 1,
      'exclude' => '', 'include' => '',
      'meta_key' => '', 'meta_value' => '',
      'authors' => '', 'parent' => -1, 'exclude_tree' => '',
      'number' => '', 'offset' => 0, 'status' => 'publish'
   );

   $r = wp_parse_args( $args, $defaults );
   extract( $r, EXTR_SKIP );
   $number = (int) $number;
   $offset = (int) $offset;

   if ($status == 'all' || $status == 'All' || $status == 'ALL') $statusquery = '';
   else $statusquery = $wpdb->prepare(" AND post_status = '". $status ."'");


   $cache = array();
   $key = md5( serialize( compact(array_keys($defaults)) ) );
   if ( $cache = wp_cache_get( 'get_pages', 'posts' ) ) {
      if ( is_array($cache) && isset( $cache[ $key ] ) ) {
         $pages = apply_filters('get_pages', $cache[ $key ], $r );
         return $pages;
      }
   }

   if ( !is_array($cache) )
      $cache = array();

   $inclusions = '';
   if ( !empty($include) ) {
      $child_of = 0; //ignore child_of, parent, exclude, meta_key, and meta_value params if using include
      $parent = -1;
      $exclude = '';
      $meta_key = '';
      $meta_value = '';
      $hierarchical = false;
      $incpages = preg_split('/[\s,]+/',$include);
      if ( count($incpages) ) {
         foreach ( $incpages as $incpage ) {
            if (empty($inclusions))
               $inclusions = $wpdb->prepare(' AND ( ID = %d ', $incpage);
            else
               $inclusions .= $wpdb->prepare(' OR ID = %d ', $incpage);
         }
      }
   }
   if (!empty($inclusions))
      $inclusions .= ')';

   $exclusions = '';
   if ( !empty($exclude) ) {
      $expages = preg_split('/[\s,]+/',$exclude);
      if ( count($expages) ) {
         foreach ( $expages as $expage ) {
            if (empty($exclusions))
               $exclusions = $wpdb->prepare(' AND ( ID <> %d ', $expage);
            else
               $exclusions .= $wpdb->prepare(' AND ID <> %d ', $expage);
         }
      }
   }
   if (!empty($exclusions))
      $exclusions .= ')';

   $author_query = '';
   if (!empty($authors)) {
      $post_authors = preg_split('/[\s,]+/',$authors);

      if ( count($post_authors) ) {
         foreach ( $post_authors as $post_author ) {
            //Do we have an author id or an author login?
            if ( 0 == intval($post_author) ) {
               $post_author = get_userdatabylogin($post_author);
               if ( empty($post_author) )
                  continue;
               if ( empty($post_author->ID) )
                  continue;
               $post_author = $post_author->ID;
            }

            if ( '' == $author_query )
               $author_query = $wpdb->prepare(' post_author = %d ', $post_author);
            else
               $author_query .= $wpdb->prepare(' OR post_author = %d ', $post_author);
         }
         if ( '' != $author_query )
            $author_query = " AND ($author_query)";
      }
   }

   $join = '';
   $where = "$exclusions $inclusions ";
   if ( ! empty( $meta_key ) || ! empty( $meta_value ) ) {
      $join = " LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )";

      // meta_key and meta_value might be slashed
      $meta_key = stripslashes($meta_key);
      $meta_value = stripslashes($meta_value);
      if ( ! empty( $meta_key ) )
         $where .= $wpdb->prepare(" AND $wpdb->postmeta.meta_key = %s", $meta_key);
      if ( ! empty( $meta_value ) )
         $where .= $wpdb->prepare(" AND $wpdb->postmeta.meta_value = %s", $meta_value);

   }

   // here we call on the existing join condition used for posts
   $join = MWX__join  ($join);

   if ( $parent >= 0 )
      $where .= $wpdb->prepare(' AND post_parent = %d ', $parent);

   // here we call on the existing where clause used for posts
   $where = MWX__where ($where);

   $query = "SELECT * FROM $wpdb->posts $join WHERE (post_type = 'page'$statusquery) $where ";
   $query .= $author_query;
   $query .= " ORDER BY " . $sort_column . " " . $sort_order ;

   if ( !empty($number) )
      $query .= ' LIMIT ' . $offset . ',' . $number;

   //echo $query;

   $pages = $wpdb->get_results($query);

   if ( empty($pages) ) {
      $pages = apply_filters('get_pages', array(), $r);
      return $pages;
   }

   // Update cache.
   update_page_cache($pages);

   if ( $child_of || $hierarchical )
      $pages = & get_page_children($child_of, $pages);

   if ( !empty($exclude_tree) ) {
      $exclude = array();

      $exclude = (int) $exclude_tree;
      $children = get_page_children($exclude, $pages);
      $excludes = array();
      foreach ( $children as $child )
         $excludes[] = $child->ID;
      $excludes[] = $exclude;
      $total = count($pages);
      for ( $i = 0; $i < $total; $i++ ) {
         if ( in_array($pages[$i]->ID, $excludes) )
            unset($pages[$i]);
      }
   }

   $cache[ $key ] = $pages;
   wp_cache_set( 'get_pages', $cache, 'posts' );

   $pages = apply_filters('get_pages', $pages, $r);

   return $pages;
}
//===========================================================================

//===========================================================================
//
// return list of pages to display as widget
//
function MWX__list_pages($args = '')
{
   $defaults = array(
      'depth' => 0, 'show_date' => '',
      'date_format' => get_option('date_format'),
      'child_of' => 0, 'exclude' => '',
      'title_li' => __('Pages'), 'echo' => 1,
      'authors' => '', 'sort_column' => 'menu_order, post_title',
      'link_before' => '', 'link_after' => ''
   );

   $r = wp_parse_args( $args, $defaults );
   extract( $r, EXTR_SKIP );

   $output = '';
   $current_page = 0;

   // sanitize, mostly to keep spaces out
   $r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);

   // Allow plugins to filter an array of excluded pages
   $r['exclude'] = implode(',', apply_filters('wp_list_pages_excludes', explode(',', $r['exclude'])));

   // Query pages.
   $r['hierarchical'] = 0;
   $pages = MWX__get_pages($r);

   if ( !empty($pages) ) {
      if ( $r['title_li'] )
         $output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

      global $wp_query;
      if ( is_page() || is_attachment() || $wp_query->is_posts_page )
         $current_page = $wp_query->get_queried_object_id();
      $output .= walk_page_tree($pages, $r['depth'], $current_page, $r);

      if ( $r['title_li'] )
         $output .= '</ul></li>';
   }

   $output = apply_filters('wp_list_pages', $output);

   if ( $r['echo'] )
      echo $output;
   else
      return $output;
}
//===========================================================================

//===========================================================================
//
// Register our widget.
// 'Example_Widget' is the widget class used below.
//
//
function MWX__load_widgets() {
   register_widget( 'widget_MWX__list_pages' );
}
//===========================================================================

//===========================================================================
//
// Example Widget class.
// This class handles everything that needs to be handled with the widget:
// the settings, form, display, and update.  Nice!
//
//
class widget_MWX__list_pages extends WP_Widget {

   function widget_MWX__list_pages() {
      $widget_ops = array('classname' => 'mwgc_widget_pages', 'description' => __( 'MemberWing Gradual Content widget to list Pages based on maturity') );
      $this->WP_Widget('memberwing_pages', __('MemberWing-X Pages'), $widget_ops);
   }

   function widget( $args, $instance ) {
      extract( $args );

      $title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'MemberWing-X Pages' ) : $instance['title']);
      $sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
      $exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];

      if ( $sortby == 'menu_order' )
         $sortby = 'menu_order, post_title';

      $out = MWX__list_pages( apply_filters('widget_pages_args', array('title_li' => '', 'echo' => 0, 'sort_column' => $sortby, 'exclude' => $exclude) ) );

      if ( !empty( $out ) ) {
         echo $before_widget;
         if ( $title)
            echo $before_title . $title . $after_title;
      ?>
      <ul>
         <?php echo $out; ?>
      </ul>
      <?php
         echo $after_widget;
      }
   }

   function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
         $instance['sortby'] = $new_instance['sortby'];
      } else {
         $instance['sortby'] = 'menu_order';
      }

      $instance['exclude'] = strip_tags( $new_instance['exclude'] );

      return $instance;
   }

   function form( $instance ) {
      //Defaults
      $instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'exclude' => '') );
      $title = esc_attr( $instance['title'] );
      $exclude = esc_attr( $instance['exclude'] );
   ?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
      <p>
         <label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
         <select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
            <option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
            <option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
            <option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
         </select>
      </p>
      <p>
         <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
         <br />
         <small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
      </p>
<?php
   }

}
//===========================================================================

//===========================================================================
//
// Returns current user's maturity in days

function MWX__get_current_user_maturity ()
{
   global $current_user;

   get_currentuserinfo();

   if (!$current_user)
      return (0);

   // Format: 2009-01-15 18:45:29
   if (!$current_user->user_registered)
      return (0);

   $user_join_datetime = $current_user->user_registered . " UTC";

   $user_maturity = floor((strtotime("now") - strtotime($user_join_datetime)) / (60*60*24));

   // Fix to allow webmasters to make posts/pages invisible to non-members but immediately visible to members.
   // This will make any logged-on member at least 1 day mature.
   if (!$user_maturity)
      $user_maturity = 1;

   return $user_maturity;
}
//===========================================================================


?>