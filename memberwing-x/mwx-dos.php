<?php

/****************************************************************************
                                 MemberWing-x
                         Digital Online Store Builder
                                    by
                                Gleb Esman
                            (c) 2010 MemberWing

                         http://www.memberwing.com
/***************************************************************************/

//===========================================================================
//
// Returns list of premium files.
/*

$params = array (
   // If no templates passed as params - the ones specified in settings will be used.
   'format'=>'html', // Optional. (default format)
   'use_template' = 't5',  // named template.
   'templates'=>array ('main_container'='...', 'directory'=>'...', 'item_allowed'='...', 'item_denied'='...'),

   // Full pattern: '@\.pdf$@i'. Matched against full relative path name: 'dir/name/file.ext'.
   'regex_include'=>'',    // Empty => all inclusive.    Evaluated first.                 Complete pattern, such as: '@\.pdf$@i'
   'regex_exclude'=>'',    // Empty => nothing excluded. Evaluated after include regex

   // Number of items to show
   'max_items'=>10,     // If unspecified, 0, FALSE or -1 => unlimited items

   // Sorting
   'sort'=>'name',      // 'name' ('name-asc') = normal sort by name, 'name-desc' - reverse sort by name, 'fndate' ('fndate-asc')- older to newer (date encoded in filename), 'date-desc' - newer to older (date encoded in filename)
   )

NOTES:
- In every conditional statement present of all 3 constructs is mandatory: {IF_*} ... {ELSE} ... {ENDIF}. IOW: {IF_*} ... {ENDIF} - without {ELSE} in between is illegal. Use {ELSE}{ENDIF}
- Conditional statement may be nested to any level: {IF_*}{IF_*} ... {ELSE}{ENDIF} ... {ELSE} ... {ENDIF}
- Loose opening curly bracket '{' in between conditional statements as part of your own markup or text is not allowed.
- "NULL" means "reset" value - to be inherited from parent
- New "Unrestricted access" directories. Allows access to all files inside them and in subdirectories to all.
- description excerpt marker: [...]
- {{MWXPHP=my_php_snippet}}  - to embed custom DOS inside posts/pages
- pass use_template 't5' in params to pull the rest from params
- DOS stores may be embedded inside of pages via tags: '[mwx-digital-online-store]', '[mwx-digital-online-store t5]' and similars with HTML code.
- Embeddable widget .js
- API to embed dos - good for SEO: http://WWW.YOUR-SITE.COM/wp-content/plugins/memberwing-x/dos-widget/mwx-dos-api.php?
- template's /js file may assume that jQuery is loaded. It is taken care by MWX, by mwx-dos-api and by mwx widget js code.
- So, build dos store via:
   -  [mwx-digital-online-store t5]    - in current blog (use this on any post/page)
   -  {{MWXPHP=php_dos_snippet}}       - to embed custom DOS inside posts/pages = if (function_exists('MWX__digital_online_store')) {echo MWX__digital_online_store(array('use_template'=>'t5'));}
   -  embed javascript widget for dos template inside any site.
   -  SEO API call on any website: <div><?php echo file_get_contents('http://YOUR-SITE.com/.../mwx-dos-api.php?format=json&use_template=t5'); ?></div>
*/

function MWX__digital_online_store ($params=FALSE)
{
   $mwx_settings     = MWX__get_settings ();
   if (!MWX__License_Allowed ($mwx_settings))
      return '<h2 align="center" style="margin:20px;padding:20px;border:2px solid red;">You must have <a href="http://toprate.org/buymwx">valid MemberWing-X license</a> to handle digital downloadable products<br /><a href="http://toprate.org/buymwx">Click here to buy MemberWing-X license</a></h2>';

   $currency_symbol  = MWX__GetCurrencySymbolHTML ($mwx_settings);
   $random_number    = substr(md5(microtime()), 0, 8);
   $regex_include    = @$params['regex_include'];
   $regex_exclude    = @$params['regex_exclude'];
   $max_items        = @$params['max_items']; // 0 or FALSE or -1 - no limit.
   $sort             = @$params['sort'];

   $total_items_rendered = 0;    // Reset items counter
   $pre_url          = rtrim(get_bloginfo ('wpurl'), '/') . '/' . $mwx_settings['protected_files_logical_addr'] . '/';

   //----------------------------------------------------------
   // Main container default template.
   if (isset($params['templates']['main_container']))
      $T_main_container = $params['templates']['main_container'];
   else
      {
      if (@$params['use_template'] && isset($mwx_settings['dos_templates'][$params['use_template']]['dos_style_main_container']))
         $T_main_container = $mwx_settings['dos_templates'][$params['use_template']]['dos_style_main_container'];
      else
         $T_main_container = $mwx_settings['dos_templates']['t1']['dos_style_main_container'];
      }
   //----------------------------------------------------------

   //----------------------------------------------------------
   // Directory default template.
   if (isset($params['templates']['directory']))
      $T_digital_item_directory = $params['templates']['directory'];
   else
      {
      if (@$params['use_template'] && isset($mwx_settings['dos_templates'][$params['use_template']]['dos_style_directory_template']))
         $T_digital_item_directory = $mwx_settings['dos_templates'][$params['use_template']]['dos_style_directory_template'];
      else
         $T_digital_item_directory = $mwx_settings['dos_templates']['t1']['dos_style_directory_template'];
      }
   //----------------------------------------------------------

   //----------------------------------------------------------
   // Allowed item default template.
   if (isset($params['templates']['item_allowed']))
      $T_digital_item_allowed = $params['templates']['item_allowed'];
   else
      {
      if (@$params['use_template'] && isset($mwx_settings['dos_templates'][$params['use_template']]['dos_style_item_allowed_template']))
         $T_digital_item_allowed = $mwx_settings['dos_templates'][$params['use_template']]['dos_style_item_allowed_template'];
      else
         $T_digital_item_allowed = $mwx_settings['dos_templates']['t1']['dos_style_item_allowed_template'];
      }
   //----------------------------------------------------------

   //----------------------------------------------------------
   // Denied item default template.
   if (isset($params['templates']['item_denied']))
      $T_digital_item_denied = $params['templates']['item_denied'];
   else
      {
      if (@$params['use_template'] && isset($mwx_settings['dos_templates'][$params['use_template']]['dos_style_item_denied_template']))
         $T_digital_item_denied = $mwx_settings['dos_templates'][$params['use_template']]['dos_style_item_denied_template'];
      else
         $T_digital_item_denied = $mwx_settings['dos_templates']['t1']['dos_style_item_denied_template'];
     }
   //----------------------------------------------------------

   //---------------------------------------
   // Get list of *all* premium files (everything under /PREMIUM_FILES/)

   $all_files       = array();
   $skip_dirs_array =                                explode (',', trim($mwx_settings['unrestricted_access_directory_names']));
   $skip_dirs_array = array_merge ($skip_dirs_array, explode (',', trim($mwx_settings['locked_access_directory_names'])));
   MWX__enumerate_files_in_dir ($mwx_settings['protected_files_physical_addr'], $all_files, TRUE, $skip_dirs_array);

   $relative_dir_chars = strlen ($mwx_settings['protected_files_physical_addr']);

   $all_html_table_rows = "";

   foreach ($all_files as $full_dirname=>$files)
      {
      //----------------------------------------------------
      // Reset directory logic variables.
      // ...
      //----------------------------------------------------

      // $dirname = 'membership/gold'
      $dirname = substr ($full_dirname, $relative_dir_chars);
      $dirname = str_replace ('\\', '/', $dirname);
      $dirname = ltrim ($dirname, '/');

      // Construct values for this dir object. Scan all the way to the parent to find values for NULL's.
      $dir_object_values = MWX__Fill_DirObject_Values ($dirname);

      $all_html_file_rows_for_this_dir = array();

      //----------------------------------------------------
      // Sort names, if requested.
      // 'uasort()' force-sort assoc keys via user defined callback.
      // 'sort'=>'name',      // 'name' ('name-asc') = normal sort by name, 'name-desc' - reverse sort by name, 'fndate' ('fndate-asc')- older to newer (date encoded in filename), 'date-desc' - newer to older (date encoded in filename)
      if ($sort)
         {
         switch ($sort)
            {
            case  'name-desc'    :
               uasort ($files, 'MWX__Sort_name_desc');
               break;

            case  'fndate'       :
            case  'fndate-asc'   :
               uasort ($files, 'MWX__Sort_fndate_asc');
               break;

            case  'fndate-desc'  :
               uasort ($files, 'MWX__Sort_fndate_desc');
               break;

            default:
            case  'name'         :
            case  'name-asc'     :
               uasort ($files, 'MWX__Sort_name_asc');
               break;
            }
         }
      //----------------------------------------------------

      foreach ($files as $file)
         {
         //----------------------------------------------------
         // Reset file logic variables.
         $LOGIC__PROD_PRICE         = FALSE;
         $LOGIC__FILE_WITH_PREVIEW  = FALSE;
         $LOGIC__CUSTOM_BUY_CODE    = FALSE;
         $LOGIC__GOOGLE             = FALSE;
         //----------------------------------------------------

         if ($dirname)
            $file_rel_name = $dirname . '/' . $file;
         else
            $file_rel_name = $file;

         //----------------------------------------------------
         // Make sure our file matches requested pattern
         if ($regex_include && !preg_match ($regex_include, $file_rel_name))
            continue;   // This file does not match requested include regex pattern.
         if ($regex_exclude && preg_match ($regex_exclude, $file_rel_name))
            continue;   // This file matches requested regex pattern for exclusion.
         //----------------------------------------------------

         // Get values for this file object.
         // WARNING: PHP is silly enough in 2010 to return filesize as signed integer. Hence correct filesize will only work for files <2GB.
         //          We can still use it as is - we only need it to be reasonably unique value to ID file.
         $filesize = @filesize ($full_dirname . '/' . $file);

         $file_object_values = MWX__Fill_FileObject_Values ($dirname, array('fs_name'=>$file, 'filesize'=>$filesize, 'is_dir'=>FALSE), $dir_object_values);

         if (!$file_object_values['is_active'])
            continue;   // Output only active objects.

         $file_icon_url_1 = explode ("\n", $file_object_values['image_urls_small']);
         $file_icon_url_1 = trim ($file_icon_url_1[0]);
         $file_bigimg_url_1 = explode ("\n", $file_object_values['image_urls']);
         $file_bigimg_url_1 = trim ($file_bigimg_url_1[0]);

         if ((float)$file_object_values['price'] == 0)
            $LOGIC__PROD_PRICE = FALSE;
         else
            $LOGIC__PROD_PRICE = TRUE;

         if (@$file_object_values['custom_buy_code'] && $file_object_values['custom_buy_code'] != 'NULL')
            $LOGIC__CUSTOM_BUY_CODE = TRUE;
         else
            $LOGIC__CUSTOM_BUY_CODE = FALSE;

         if (MWX__visit_is_search_engine_spider ())
            $LOGIC__GOOGLE = TRUE;
         else
            $LOGIC__GOOGLE = FALSE;

         if (MWX__DCP_User_Allowed_To_Access_Object ($file_rel_name, $mwx_settings))
            {
            if (!$T_digital_item_allowed)
               continue;   // No rendering requested for allowed files
            $buy_buttons = FALSE;
            $html_table_row = $T_digital_item_allowed;
            }
         else
            {
            if (!$T_digital_item_denied)
               continue;   // No rendering requested for denied files
            $html_table_row = $T_digital_item_denied;

            if ((float)$file_object_values['price'] == 0)
               $buy_buttons = FALSE;
            else
               {
               if ($mwx_settings['authnet_postback_integration_enabled'] && $mwx_settings['dos_primary_payment_processor']=='authorize.net')
                  $buy_buttons = MWX__CreateAuthorizeNetButtons (0, $file_object_values['prodname'] . " [file: $file_rel_name]", $file_object_values['price'], $mwx_settings);
               else
                  $buy_buttons = MWX__CreatePaypalButtons       (0, $file_object_values['prodname'] . " [file: $file_rel_name]", $file_object_values['price'], $mwx_settings);
               }

            ///!!! NOTE: searching for matching '_denied' file must be moved to separate function because it must scan previous directories as well
            if (file_exists (preg_replace ('@^(.*?)((\.[^\.]+)?)$@', "$1_denied$2", $full_dirname . '/' . $file)))
               $LOGIC__FILE_WITH_PREVIEW = TRUE;
            else
               $LOGIC__FILE_WITH_PREVIEW = FALSE;
            }

         if (!$html_table_row)
            continue;

         // Custom code may contain MWX DOS tags so we process it first.
         $html_table_row = str_replace ('{CUSTOM_BUY_CODE}',      @$file_object_values['custom_buy_code'],     $html_table_row);
         $html_table_row = str_replace ('{ICON_URL_1}',           $file_icon_url_1,                            $html_table_row);
         $html_table_row = str_replace ('{BIGIMG_URL_1}',         $file_bigimg_url_1,                          $html_table_row);
         $html_table_row = str_replace ('{ICON_IMGS_2X}',         '',                            $html_table_row);
         $html_table_row = str_replace ('{PROD_NAME}',            $file_object_values['prodname']?$file_object_values['prodname']:$file,   $html_table_row);
         $file1 = $file; //preg_replace ('@\s*\d\d\d\d\-\d\d\-\d\d\.@', '.', $file);
         $html_table_row = str_replace ('{PROD_NAME_TXT}',        $file_object_values['prodname']?$file_object_values['prodname']:(ucwords(preg_replace ('@[\-\_\s]+@', ' ', $file1))),   $html_table_row);
         $html_table_row = str_replace ('{PROD_DESCRIPTION}',           str_replace('[...]', '', $file_object_values['description']),                    $html_table_row);
         $html_table_row = str_replace ('{PROD_DESCRIPTION_EXCERPT}',   preg_replace('@^(.*?)\[\.\.\.\].*$@', "$1...", $file_object_values['description']),   $html_table_row);
         $html_table_row = str_replace ('{CURRENCY_SYMBOL}',      $currency_symbol,                            $html_table_row);
         $html_table_row = str_replace ('{PROD_PRICE}',           $file_object_values['price'],                $html_table_row);
         $html_table_row = str_replace ('{PROD_PRICE_TXT}',       $file_object_values['price']?($currency_symbol.$file_object_values['price']):(""),  $html_table_row);
         $html_table_row = str_replace ('{SAFE_DOWNLOAD_URL}',    $pre_url . $file_rel_name,                   $html_table_row);
         $html_table_row = str_replace ('{RANDOM_NUMBER}',        $random_number,                              $html_table_row);
         $html_table_row = str_replace ('{DIR_NAME}',             $dirname?($dirname.'/'):"",                  $html_table_row);
         $html_table_row = str_replace ('{FILE_NAME}',            $file,                                       $html_table_row);
         $html_table_row = str_replace ('{BUY_NOW_BUTTON}',       @$buy_buttons['buy_now_button_code'],        $html_table_row);
         $html_table_row = str_replace ('{ADD_TO_CART_BUTTON}',   @$buy_buttons['add_to_cart_button_code'],    $html_table_row);
         $html_table_row = str_replace ('{URL_DOWNLOAD_BUTTON}',  $mwx_settings['download_button_image'],      $html_table_row);

         //----------------------------------------------------
         // After all variables substitution - perform first step of conditional logic on file row - replace {IF_...} with {IF_TRUE} and {IF_FALSE}
         $html_table_row = str_replace ('{IF_FILE_WITH_PREVIEW}', $LOGIC__FILE_WITH_PREVIEW?'{IF_TRUE}':'{IF_FALSE}',   $html_table_row);
         $html_table_row = str_replace ('{IF_PROD_PRICE}',        $LOGIC__PROD_PRICE?'{IF_TRUE}':'{IF_FALSE}',          $html_table_row);
         $html_table_row = str_replace ('{IF_CUSTOM_BUY_CODE}',   $LOGIC__CUSTOM_BUY_CODE?'{IF_TRUE}':'{IF_FALSE}',     $html_table_row);
         $html_table_row = str_replace ('{IF_GOOGLE}',            $LOGIC__GOOGLE?'{IF_TRUE}':'{IF_FALSE}',              $html_table_row);
         //----------------------------------------------------

         $all_html_file_rows_for_this_dir[] = $html_table_row;

         //----------------------------------------------------
         // Counter
         $total_items_rendered ++;
         if ($max_items>0 && $total_items_rendered >= $max_items)
            break;
         //----------------------------------------------------
         }

      if (count($all_html_file_rows_for_this_dir))
         {
         $html_dir_row = $T_digital_item_directory;
         $html_dir_row = str_replace ('{DIR_PRODS_NAME}',       $dir_object_values['prodname'],    $html_dir_row);
         $html_dir_row = str_replace ('{DIR_PRODS_NAME_TXT}',   $dir_object_values['prodname']?$dir_object_values['prodname']:($dirname?$dirname:"/"), $html_dir_row);
         $html_dir_row = str_replace ('{DIR_PRODS_DESCRIPTION}',$dir_object_values['description'], $html_dir_row);

         //----------------------------------------------------
         // Perform first step of conditional logic on directory row - replace {IF_...} with {IF_TRUE} and {IF_FALSE}
         // ...
         //----------------------------------------------------

         $all_html_this_dir_and_files_rows = $html_dir_row . implode($all_html_file_rows_for_this_dir);

         //----------------------------------------------------
         // Perform second step of conditional logic: melt {IF_TRUE}{IF_FALSE} / {ELSE} / {ENDIF} statements, progressively from inner to outer ones.
         // ...
         $count1=$count2=0;
         do
            {
            $all_html_this_dir_and_files_rows = preg_replace ('@\{IF_TRUE\}([^\{]*)\{ELSE\}[^\{]*\{ENDIF\}@s', "$1",  $all_html_this_dir_and_files_rows, -1, $count1);
            $all_html_this_dir_and_files_rows = preg_replace ('@\{IF_FALSE\}[^\{]*\{ELSE\}([^\{]*)\{ENDIF\}@s', "$1", $all_html_this_dir_and_files_rows, -1, $count2);
            }
         while ($count1 || $count2);
         //----------------------------------------------------

         $all_html_table_rows .= $all_html_this_dir_and_files_rows;
         }

      if ($max_items>0 && $total_items_rendered >= $max_items)
         break;
      }

   $complete_html_table = str_replace ('{DOS_MWX_FOOTER}',  $mwx_settings['dos_mwx_footer_html'], $T_main_container);
   $complete_html_table = str_replace ('{ONLINE_STORE_ITEMS_HTML}', $all_html_table_rows, $complete_html_table);
   //---------------------------------------

   return ($complete_html_table);
}

//---------------------------------------------------------------------------
// Sorting callback functions
function MWX__Sort_name_asc ($a, $b)
   {
   return (strcasecmp ($a, $b));
   }
function MWX__Sort_name_desc ($a, $b)
   {
   return (strcasecmp ($b, $a));
   }
// Sorting: older to newer (date encoded in filename)
function MWX__Sort_fndate_asc ($a, $b)
   {
   // "MyDoc_2011-01-12.pdf" -> "2011-01-12MyDoc_.pdf"
   $name1 = preg_replace ('@(.*?)(\d\d\d\d\-\d\d\-\d\d)(\..*)@', "$2$1$3", $a);
   $name2 = preg_replace ('@(.*?)(\d\d\d\d\-\d\d\-\d\d)(\..*)@', "$2$1$3", $b);
   return (strcasecmp ($name1, $name2));
   }
// Sorting: newer to older (date encoded in filename)
function MWX__Sort_fndate_desc ($a, $b)
   {
   // "MyDoc_2011-01-12.pdf" -> "2011-01-12MyDoc_.pdf"
   $name1 = preg_replace ('@(.*?)(\d\d\d\d\-\d\d\-\d\d)(\..*)@', "$2$1$3", $a);
   $name2 = preg_replace ('@(.*?)(\d\d\d\d\-\d\d\-\d\d)(\..*)@', "$2$1$3", $b);
   return (strcasecmp ($name2, $name1));
   }
//---------------------------------------------------------------------------

//===========================================================================

//===========================================================================
function MWX__DB_Read_Digital_Object ($fs_name, $size, $is_dir)
{
   global $wpdb;
   $digital_objects_table_name   = DIGITAL_OBJECTS_TABLE;

   $sql_query = "SELECT * FROM `$digital_objects_table_name` WHERE `fs_name`='$fs_name' AND `filesize`='$size' AND `is_dir`='$is_dir' LIMIT 1";
   $rows_arr = $wpdb->get_results($sql_query, ARRAY_A);

   if (is_array($rows_arr) && is_array(@$rows_arr[0]))
      {
      $row = $rows_arr[0];
      foreach ($row as $k=>$v)
         {
         if (!is_null($v))
            $row[$k] = stripslashes ($v);
         }
      return $row;
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
//
// UPDATE or if not exist - CREATE digital object.
// '$object_info_array' - assoc array of UN-escaped values ready for table insertion.

function MWX__DB_UpdateCreate_Digital_Object ($object_info_array)
{

   if (@$object_info_array['inputs_crc'])
      {
      if ($object_info_array['inputs_crc'] == MWX__GetDigitalObjectValues_CRC ($object_info_array))
         return 2;   // Settings did not change
      else
         unset ($object_info_array['inputs_crc']); // Remove it from array now.
      }

   global $wpdb;
   $digital_objects_table_name   = DIGITAL_OBJECTS_TABLE;

   //----------------------------------------------------------
   // Construct 'UPDATE' SQL query.
   $sql_query = "UPDATE `$digital_objects_table_name` SET ";
   foreach ($object_info_array as $k=>$v)
      {
      if ($k != 'id' && $k != 'fs_name' && $k != 'filesize' && $k != 'is_dir')
         {
         if ($v != 'NULL')
            {
            $v = esc_sql (stripslashes(html_entity_decode($v)));
            $sql_query .= ("`$k`='$v', ");
            }
         else
            {
            $sql_query .= ("`$k`=NULL, ");
            }
         }
      }
   $sql_query = rtrim ($sql_query, ' ,');
   $sql_query .= " WHERE ";

   if (@$object_info_array['id'])
      $sql_query .= "`id`='{$object_info_array['id']}'";
   else
      $sql_query .= "`fs_name`='{$object_info_array['fs_name']}' AND `filesize`='{$object_info_array['filesize']}' AND `is_dir`='{$object_info_array['is_dir']}'";
   //----------------------------------------------------------

   $RetCode = $wpdb->query ($sql_query);  // The function returns an integer corresponding to the number of rows affected/selected, FALSE on error.

   if (!$RetCode)
      {
      // UPDATE failed. Probably record does not exist yet. INSERT-ing it.
      $sql_query = "INSERT INTO `$digital_objects_table_name` ";
      $sql_keys  = "";
      $sql_vals  = "";

      foreach ($object_info_array as $k=>$v)
         {
         if ($k != 'id')
            {
            $v = esc_sql (stripslashes(html_entity_decode($v)));
            $sql_keys .= "`$k`, ";
            if ($v === NULL || $v == 'NULL')
               $sql_vals .= "NULL, ";
            else
               $sql_vals .= "'$v', ";
            }
         }
      $sql_keys = rtrim ($sql_keys, ' ,');
      $sql_vals = rtrim ($sql_vals, ' ,');
      $sql_query .= ( "(" . $sql_keys . ") VALUES (" . $sql_vals . ")" );


      $RetCode = $wpdb->query ($sql_query);

      }

   return $RetCode;
}
//===========================================================================

//===========================================================================
//
// DELETE digital object by 'id' or by fs_name, filesize and is_dir
function MWX__DB_Delete_Digital_Object ($object_info_array)
{
   global $wpdb;
   $digital_objects_table_name   = DIGITAL_OBJECTS_TABLE;

   if (isset($object_info_array['id']))
      $sql_query = "DELETE FROM `$digital_objects_table_name` WHERE `id`='{$object_info_array['id']}'";
   else
      $sql_query = "DELETE FROM `$digital_objects_table_name` WHERE `fs_name`='{$object_info_array['fs_name']}' AND `filesize`='{$object_info_array['filesize']}' AND `is_dir`='{$object_info_array['is_dir']}'";

   $RetCode = $wpdb->query ($sql_query);
   return $RetCode;
}
//===========================================================================

//===========================================================================
// Construct all values for this object, including vars that are not set (set to NULL in DB).
// Scan all the way to the parent dirs to find it out NULLs.

// $dirname - non-slashed relative dirnames: 'some/dir/name' - mandatory for all (dirs and files)
// On return all values are guaranteed to be pre-filled at least with empty strings.
//
// NOTE: prodname and description is not inheritable from upper directories

function MWX__Fill_DirObject_Values ($dirname)
{

   $ready_values = array ('prodname'=>NULL, 'description'=>NULL, 'image_urls'=>NULL, 'image_urls_small'=>NULL, 'price'=>NULL, 'custom_buy_code'=>NULL, 'is_active'=>NULL);

   $first_time = TRUE;

   // Read all dirs
   do
      {
      $this_dirobject_values = MWX__DB_Read_Digital_Object ($dirname, 0, TRUE);
      if ($first_time)
         $first_time = FALSE;
      else
         {
         // NOTE: prodname and description is not inheritable from upper directories
         $this_dirobject_values['prodname']     =  NULL;
         $this_dirobject_values['description']  =  NULL;
         }

      if (is_array($this_dirobject_values))
         {
         foreach ($ready_values as $k=>$v)
            {
            if (($v === NULL || $v == 'NULL') && (@$this_dirobject_values[$k] !== NULL && @$this_dirobject_values[$k] != 'NULL'))
               {
               // Overwrite only NULL ones with closest parent's non-NULL
               $ready_values[$k] = $this_dirobject_values[$k];
               }
            }
         }

      if (!$dirname)
         break;

      // 'dir1/dir2/dir3' -> 'dir1/dir2' -> 'dir1' -> ''
      $dirname = preg_replace ('@(^|/)[^/]+$@', '', $dirname);
      }
   while (1);

   return $ready_values;
}
//===========================================================================

//===========================================================================
// Construct all values for this object, including vars that are not set (set to NULL in DB).
// Scan all the way to the parent dirs to find it out NULLs.

// $dirname - mandatory for files: 'dir1/dir2/dir3'
// $file_object_values - must contain essentials: name, size, is_dir
// $dir_object_values - optional pre-filled values for hierarchy of dirs of this file
// On return all values are guaranteed to be pre-filled at least with empty strings.
//
// NOTE: prodname and description is not inheritable from directories

function MWX__Fill_FileObject_Values ($dirname, $file_object_values, $dir_object_values=FALSE)
{

   $this_fileobject_values = MWX__DB_Read_Digital_Object ($file_object_values['fs_name'], $file_object_values['filesize'], FALSE);

   if (!$this_fileobject_values)
      {
      $this_fileobject_values = $file_object_values;
      $this_fileobject_values['image_urls']        =  NULL;
      $this_fileobject_values['image_urls_small']  =  NULL;
      $this_fileobject_values['price']             =  NULL;
      $this_fileobject_values['custom_buy_code']   =  NULL;
      $this_fileobject_values['is_active']         =  NULL;
      }

   // Enforce zero price for objects located inside of 'free' dirs: 'some/free/dir/ebook.pdf'.
   if (in_array('free', (explode('/', $dirname))))
      $this_fileobject_values['price'] = '0';

   if (
      $this_fileobject_values['image_urls'] !== NULL &&
      $this_fileobject_values['image_urls'] !=  'NULL' &&
      $this_fileobject_values['image_urls_small'] !== NULL &&
      $this_fileobject_values['image_urls_small'] != 'NULL' &&
      $this_fileobject_values['price'] !== NULL &&
      $this_fileobject_values['price'] != 'NULL' &&
      $this_fileobject_values['custom_buy_code'] !== NULL &&
      $this_fileobject_values['custom_buy_code'] != 'NULL' &&
      $this_fileobject_values['is_active'] !== NULL &&
      $this_fileobject_values['is_active'] != 'NULL'
      )
      return ($this_fileobject_values);

   if (!$dir_object_values)
      $ready_values = MWX__Fill_DirObject_Values ($dirname);
   else
      $ready_values = $dir_object_values;
   // NOTE: prodname and description is not inheritable from directories
   $ready_values['prodname']     =  NULL;
   $ready_values['description']  =  NULL;

   foreach ($this_fileobject_values as $k=>$v)
      {
      if ($v !== NULL && $v != 'NULL')
         {
         // This fileobject non-NULL values are highest priority
         $ready_values[$k] = $v;
         }
      }

   return $ready_values;
}
//===========================================================================

//===========================================================================
// Calculate CRC of editable values of digital object
function MWX__GetDigitalObjectValues_CRC ($dig_object)
{
///!!! Soon add CRC calcs for TAGS and CATEGORIES
   $str = @$dig_object['prodname'] . @$dig_object['description'] . @$dig_object['image_urls'] . @$dig_object['image_urls_small'] . @$dig_object['price'] . @$dig_object['custom_buy_code'] . @$dig_object['is_active'];
   return md5 ($str);
}
//===========================================================================


?>