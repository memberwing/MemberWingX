<?php

// Define table names
define('DIGITAL_OBJECTS_TABLE',     'MWXDCP_digital_objects');
define('TAGS_TABLE',                'MWXDCP_tags');
define('CATEGORIES_TABLE',          'MWXDCP_categories');
define('OBJECTS__TAGS_TABLE',       'MWXDCP_objects__tags');
define('OBJECTS__CATEGORIES_TABLE', 'MWXDCP_objects__categories');

//===========================================================================
// Create DCP/DB tables
function MWX__DOS_create_database_tables ()
{
   if (!MWX__License_Allowed ())
      return;

   // Create database tables if not exist
   global $wpdb;

   $digital_objects_table_name   = DIGITAL_OBJECTS_TABLE;
   $tags_table_name              = TAGS_TABLE;
   $categories_table_name        = CATEGORIES_TABLE;
   $obj__tags_table_name         = OBJECTS__TAGS_TABLE;
   $obj__categories_table_name   = OBJECTS__CATEGORIES_TABLE;

   if($wpdb->get_var("SHOW TABLES LIKE '$digital_objects_table_name'") != $digital_objects_table_name)
      $b_first_time = TRUE;
   else
      $b_first_time = FALSE;

   //----------------------------------------------------------
   // Create tables
   $query = "CREATE TABLE IF NOT EXISTS `$digital_objects_table_name` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `fs_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Non-slashed name of object on filesystem. Could be dir or filename',
      `filesize` bigint(20) NOT NULL,
      `prodname` text COLLATE utf8_unicode_ci,
      `description` text COLLATE utf8_unicode_ci,
      `image_urls` text COLLATE utf8_unicode_ci DEFAULT NULL,
      `image_urls_small` text COLLATE utf8_unicode_ci DEFAULT NULL,
      `price` float DEFAULT NULL,
      `custom_buy_code` text default NULL,
      `is_dir` tinyint(1) NOT NULL,
      `is_active` tinyint(1) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `fs_name__filesize` (`fs_name`,`filesize`)
      )";
   $wpdb->query ($query);

   $query = "CREATE TABLE IF NOT EXISTS `$tags_table_name` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `name` char(128) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `name` (`name`)
      )";
   $wpdb->query ($query);

   $query = "CREATE TABLE IF NOT EXISTS `$categories_table_name` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `parent_category_id` bigint(20) unsigned NOT NULL DEFAULT '0',
      `name` char(128) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `parent__name` (`parent_category_id`,`name`)
      )";
   $wpdb->query ($query);

   $query = "CREATE TABLE IF NOT EXISTS `$obj__tags_table_name` (
      `object_id` bigint(20) NOT NULL,
      `tag_id` bigint(20) NOT NULL,
      UNIQUE KEY `oid__tid` (`object_id`,`tag_id`)
      )";
   $wpdb->query ($query);

   $query = "CREATE TABLE IF NOT EXISTS `$obj__categories_table_name` (
      `object_id` bigint(11) NOT NULL,
      `category_id` bigint(20) NOT NULL,
      UNIQUE KEY `oid__catid` (`object_id`,`category_id`)
      )";
   $wpdb->query ($query);
   //----------------------------------------------------------


   if ($b_first_time)
      {
      // Set defaults

      // If first time - populate tables with default values
      // Note: may use $wpdb->escape('...'); to sanitize inputs
      //
      global $g_MWX__plugin_directory_url;
      $icon_url = esc_sql($g_MWX__plugin_directory_url . '/css/dos-builder/t1/download.png');

      $query = "INSERT INTO `$digital_objects_table_name`
         (`fs_name`, `filesize`, `image_urls`, `image_urls_small`, `price`, `is_dir`, `is_active`)
            VALUES
         ('', '0', '$icon_url', '$icon_url', '49.95', '1', '1')";
      $wpdb->query ($query);
      }
}
//===========================================================================


//===========================================================================
// Called from mwx-admin.php : MWX__render_dos_settings_page_html()
function MWX__render_dos_settings_page_html_2 ()
{
   MWX__render_dos_settings_page_html_FilesList ();
   MWX__render_dos_settings_page_html_Settings ();
   MWX__render_dos_settings_page_html_Instructions ();
}
//===========================================================================

//===========================================================================
function MWX__render_dos_settings_page_html_Settings ()
{
   $mwx_settings = MWX__get_settings ();

   //---------------------------------------
   // Check if license is valid
   if (!MWX__License_Allowed ($mwx_settings))
      $is_disabled = ' disabled="disabled" ';
   else
      $is_disabled = '';
   //---------------------------------------

   // Detect currently active template
   if (isset($_REQUEST['dos_current_active_template']) && is_array($_REQUEST['dos_current_active_template']))
      {
      foreach ($_REQUEST['dos_current_active_template'] as $active_template=>$v)
         break;

      $mwx_settings['dos_current_active_template'] = $active_template;
      MWX__update_settings ($mwx_settings);
      }
   else
      $active_template = $mwx_settings['dos_current_active_template'];

   // Make sure currently active template has valid data in it. If not - use data from 't1' template
   foreach ($mwx_settings['dos_templates']['t1'] as $k=>$v)
      {
      if (!isset($mwx_settings['dos_templates'][$active_template][$k]))
         {
         $mwx_settings['dos_templates'][$active_template][$k] = $v;
         }
      }

   //---------------------------------------
   // Initialize embeddable widget code: $mwx_dos_widget_code
   $widget_prefix       = substr(md5(microtime()), 0, 4);
   $use_template        = $active_template;
   $regex_include       = "";
   $regex_exclude       = "";
   $max_items           = -1;
   $sort                = 'name-asc';
   global $g_MWX__plugin_directory_url;
   $widget_dir_url      = $g_MWX__plugin_directory_url . '/dos-widget';

   $T_mwx_dos_widget_code =<<<TTT
<!-- MemberWing-X Digital Online Store Widget, www.MemberWing.com, CODE START -->
<script type="text/javascript">
   var mwx_widget_$widget_prefix = {t:{
      use_template   :  '$use_template',
      regex_include  :  '$regex_include', // Ex:  '@\.pdf$@i' or '@reports/@'
      regex_exclude  :  '$regex_exclude',
      max_items      :  {MAX_ITEMS},
      sort           :  '$sort'           // 'name-asc', 'name-desc', 'fndate-asc', 'fndate-desc'.
      },
      api_url        :  '$widget_dir_url/mwx-dos-api.php'
      };
</script>
<div class="mwx_dos_widget_$widget_prefix">
   <div style="font-size:9px;"><a href="http://www.memberwing.com/">wordpress membership plugin MemberWing-X</a></div>
</div>
<script type="text/javascript" src="$widget_dir_url/widget.js.php?prefix=$widget_prefix"></script>
<!-- MemberWing-X Digital Online Store Widget CODE END -->
TTT;

   $mwx_dos_widget_code       = str_replace ('{MAX_ITEMS}',  $max_items,   $T_mwx_dos_widget_code);
   $mwx_dos_widget_code_demo  = str_replace ('{MAX_ITEMS}',  5,            $T_mwx_dos_widget_code);
   //---------------------------------------

?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
   <table style="background-color:#555;" border="1">
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" <?php echo $is_disabled; ?>/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($active_template == 't1') : ?>
<?php /* Resetting page settings will be applied only to 't1' template (due to the way MWX__reset_partial_settings () works), regardless of which template is currently active. So we enable this button only for 't1' */ ?>
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php endif; ?>
            <input type="submit" style="border:3px solid red;background-color:#FFA;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');" <?php echo $is_disabled; ?>/>
         </div></td>
      </tr>

      <tr>
         <td style="background-color:#B5FFA8" width="20%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Name</div></td>
         <td style="background-color:#B5FFA8" width="50%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Value</div></td>
         <td style="background-color:#B5FFA8" width="30%"><div align="center" style="padding:5px 3px;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 18px;">Notes</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Primary Payment Processor for digital online store:</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <select name="dos_primary_payment_processor" size="1">
                 <option value="paypal"        <?php echo ($mwx_settings['dos_primary_payment_processor']=='paypal' || !$mwx_settings['authnet_postback_integration_enabled'])?'selected="selected"':""; ?> >Paypal</option>
                 <option value="authorize.net" <?php echo (!$mwx_settings['authnet_postback_integration_enabled'])?'disabled="disabled"':''; ?> <?php echo ($mwx_settings['dos_primary_payment_processor']=='authorize.net' && $mwx_settings['authnet_postback_integration_enabled'])?'selected="selected"':""; ?> >Authorize.net</option>
               </select>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Select payment processor to be used.<br />To use authorize.net as a payment processor you need to have <b><a href="http://toprate.org/auth-net" target="_blank">Authorize.net plugin</a></b> installed, activated <b>and</b> have integration with Authorize.net enabled and configured in MemberWingX-&gt;Integration with Other Systems.</div></td>
      </tr>
      <tr>
         <td colspan="3" style="background-color:white;"><div align="center" style="padding:3px;background-color:#EEF;"><b>Digital Online Store Builder Templates</b><br />These templates will be used to render all content of digital online store on your site<br />Currently editing template: <span style="font-size:140%;font-weight:bold;color:red;"><?php echo $active_template; ?></span></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Load template</div></td>
         <td style="background-color:#CCC;">
            <div align="center">
               <?php for ($i=1; $i<11; $i++) { ?>
                  <?php
                     $button_name_value   = "name=\"dos_current_active_template[t$i]\" value=\" t$i \"";
                     if ($active_template == "t$i")
                        {
                        $button_style        = 'border:2px solid red;font-weight:bold;background-color:#EEE;';
                        $maybe_disabled      = 'disabled="disabled"';
                        }
                     else
                        {
                        $button_style        = 'border:2px solid #555;font-weight:bold;background-color:#FFE;';
                        $maybe_disabled      = '';
                        }
                  ?>
                  <input type="submit" style="<?php echo $button_style; ?>" <?php echo $button_name_value; ?> onClick="return confirm('About to load new template - make sure you already saved any changes on the current screen. Proceed?');" <?php echo $maybe_disabled; ?> />&nbsp;&nbsp;
               <?php } ?>
            </div>
         </td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Load/edit settings for another template. You may use up to 10 different templates to customize look and feel of your online store</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">URL of template's<br />stylesheet file:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="dos_templates[<?php echo $active_template; ?>][dos_style_stylesheet_file]" value="<?php echo $mwx_settings['dos_templates'][$active_template]['dos_style_stylesheet_file']; ?>" size="110" <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of stylesheet .css file to be used to render Digital Online Store on pages of your site. <b>Notes:</b><br />- Leave at default value if no extra stlyesheet is required.<br />- MemberWing-X plugin directory is <b>erased</b> and all files are overwritten during the upgrade. Keep your custom stylesheet files away from MemberWing-X plugin directory location.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">URL of template's<br />javascript file:</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="dos_templates[<?php echo $active_template; ?>][dos_style_javascript_file]" value="<?php echo $mwx_settings['dos_templates'][$active_template]['dos_style_javascript_file']; ?>" size="110" <?php echo $is_disabled; ?>/></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">URL of javascript .js file to be used by Digital Online Store. <b>Notes:</b><br />- Leave at default value if no extra javascript is required.<br />- MemberWing-X plugin directory is <b>erased</b> and all files are overwritten during the upgrade. Keep your custom javascript files away from MemberWing-X plugin directory location.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Main Container</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="overflow:auto;font-size:10px;" name="dos_templates[<?php echo $active_template; ?>][dos_style_main_container]" cols=120 rows=4 <?php echo $is_disabled; ?>><?php echo $mwx_settings['dos_templates'][$active_template]['dos_style_main_container']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This template defines main HTML container to render your Digital Online Store.<br />You decide whether it is table, list or some other custom HTML layout.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Directory/Folder template</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="overflow:auto;font-size:10px;" name="dos_templates[<?php echo $active_template; ?>][dos_style_directory_template]" cols=120 rows=4 <?php echo $is_disabled; ?>><?php echo $mwx_settings['dos_templates'][$active_template]['dos_style_directory_template']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This template will be used to render directory/folders where other digital items are located. They serve as separator/header that could give extra information about all files inside of them.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Item allowed template</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="overflow:auto;overflow-x:scroll;font-size:10px;" name="dos_templates[<?php echo $active_template; ?>][dos_style_item_allowed_template]" cols=120 rows=4 <?php echo $is_disabled; ?>><?php echo $mwx_settings['dos_templates'][$active_template]['dos_style_item_allowed_template']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This template will be used to render digital products/items for which currently logged on user already has access.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Item denied template</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="overflow:auto;overflow-x:scroll;font-size:10px;" name="dos_templates[<?php echo $active_template; ?>][dos_style_item_denied_template]" cols=120 rows=4 <?php echo $is_disabled; ?>><?php echo $mwx_settings['dos_templates'][$active_template]['dos_style_item_denied_template']; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">This template will be used to render digital products/items for which user does not have access to. User will either need to purchase these items or log in to get access to already purchased or accessable items.</div></td>
      </tr>
      <tr>
         <td colspan="3" style="background-color:#EEE;"><div align="center" style="padding-left:5px;">Embeddable Digital Online Store Widget for template: <span style="font-size:140%;font-weight:bold;color:red;"><?php echo $active_template; ?></span></div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Your Online Store Widget</div></td>
         <td style="background-color:#CCC;"><div align="center"><textarea style="overflow:auto;overflow-x:scroll;font-size:10px;border:3px solid #39F;background-color:#FFC;" cols=120 rows=4 readonly="readonly" onclick="this.select();"><?php echo $mwx_dos_widget_code; ?></textarea></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">Embeddable widget for your digital online store. Copy and paste this code onto any page of any website that supports javascript.<br />Digital Online Store Widget is a very powerful marketing tool in your online toolbox. This widget allows you to quickly replicate your gidital online store into any number of pages on any number of other websites. This could greatly help you to increase exposure to new customers and boost your sales.</div></td>
      </tr>
      <tr>
         <td style="background-color:white;"><div align="left" style="padding-left:5px;">Digital Online Store API endpoint</div></td>
         <td style="background-color:#CCC;"><div align="center"><input type="text" name="dos_api_endpoint" value="<?php echo $mwx_settings['dos_api_endpoint']; ?>" size="120" readonly="readonly" onclick="this.select();" /></div></td>
         <td style="background-color:white;"><div align="left" style="padding:5px;font-size:85%;line-height:110%;">API endpoint to build (replicate) your digital online store from other websites, blogs and CMS'es.</div></td>
      </tr>
      <tr>
         <td colspan="3" style="background-color:white;">
            <div align="center" style="padding:0 2px 2px 2px;margin:2px;border:3px solid gray;">
               <div align="center" style="padding:0 5px 5px 5px;font-size:88%;background-color:#FFF;width:80%;">
                  <div style="background-color:#FED;border:3px solid gray;border-top:none;margin-bottom:4px;">
                     This is actual sample demo of your digital online store using current settings and currently chosen template<br />
                     (make sure you upload some products under /PREMIUM_FILES/ directory)<br />
                     Insert this tag into any page/post on your site: <b>[mwx-digital-online-store <?php echo $use_template; ?>]</b><br />
                     or use any other method described below to build online store on pages of your sites.
                  </div>
                  <?php echo $mwx_dos_widget_code_demo; ?>
               </div>
            </div>
        </td>
      </tr>
      <tr>
         <td colspan="3"><div align="center" style="padding:10px 0;">
            <input type="submit" style="border:3px solid green;background-color:#D8FFD8;" name="button_update_mwx_settings" value="Save Settings" <?php echo $is_disabled; ?>/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($active_template == 't1') : ?>
            <input type="submit" style="border:1px solid red;"                            name="button_reset_partial_mwx_settings" value="Reset settings on this page to defaults" onClick="return confirm('Are you sure you want to reset settings on this page to defaults?');"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php endif; ?>
            <input type="submit" style="border:3px solid red;background-color:#FFA;"      name="button_reset_mwx_settings" value="Reset ALL settings to all defaults" onClick="return confirm('Are you sure you want to reset ALL settings on ALL pages to defaults?');" <?php echo $is_disabled; ?>/>
         </div></td>
      </tr>
   </table>
</form>

<?php
}
//===========================================================================

//===========================================================================
// Called from mwx-admin.php : MWX__render_dos_settings_page_html()
function MWX__render_dos_settings_page_html_FilesList ()
{
   $mwx_settings = MWX__get_settings ();

   //---------------------------------------
   // Check if license is valid
   if (!MWX__License_Allowed ($mwx_settings))
      {
      $is_disabled = ' disabled="disabled" ';
      echo MWX__get_admin_license_error_message ($mwx_settings);
      }
   else
      $is_disabled = '';
   //---------------------------------------

?>
<div align="center" style="font-family: Georgia, 'Times New Roman', Times, serif;font-size:18px;margin:30px 0 30px;background-color:#b8d6fb;padding:14px;border:1px solid gray;">
   MemberWing-X<br />
   <div style="color:#A00;font-size:130%;margin-top:10px;">Digital Online Store Builder Settings</div>
</div>

<?php

   $T_complete_listing=<<<TTT
   <form method="post" action="{$_SERVER['REQUEST_URI']}">
      <div id="dirs_listing_div" align="center" style="margin:15px 0;">
         <!-- Brief directory listed -->
         <table id="dirs_listing_table" width="100%">
            <tr>
                <th><div align="center"><i>Files list</i></div></th>
                <th><div align="center"><i>Dir Settings</i></div></th>
                <th><div align="center">Icon</div></th>
                <th><div align="center">Directory name</div></th>
                <th><div align="center">Default price</div></th>
                <th><div align="center">Active?</div></th>
            </tr>
{DIRS_ROWS}
         </table>
      </div>
   </form>
TTT;

   $T_dir_row=<<<TTT
            <tr class="tr_visible">
                <td><div align="center"><div id="x_dir_{DIR_NUM}" class="arrow"></div></div></td>
                <td><div align="center"><div id="e_dir_{DIR_NUM}" class="arrow2"></div></div></td>
                <td><div align="center"><img src="{DIR_ICON_URL}" height="18" /></div></td>
                <td><div align="left">{DIR_NAME}</div></td>
                <td><div align="center">{CURRENCY_SYMBOL}{DIR_PRICE}</div></td>
                <td><div align="center">{DIR_IS_ACTIVE}</div></td>
            </tr>
            <tr class="e_dir_{DIR_NUM}" style="display:none;">
                <td colspan="6">
                    <div align="center" style="background-color:#FFC;border:3px solid #e32d2d;padding:5px;margin:5px;">Editing default settings for directory <b>{DIR_NAME}</b><br />These settings will be applied to all files under this directory. You may edit individual settings for each file and subdirectory individually. Enter <b>NULL</b> to inherit default settings. </div>
                    <table width="100%">
                        <tr>
                            <th><div align="center">Products name</div></th>
                            <th><div align="center">Products description</div></th>
                            <th><div align="center">Large images URLs</div></th>
                            <th><div align="center">Small images URLs</div></th>
                            <th><div align="center">Cat.</div></th>
                            <th><div align="center">Tags</div></th>
                            <th><div align="center">Price</div></th>
                            <th><div align="center">Custom Buy Code</div></th>
                            <th><div align="center">Active?</div></th>
                        </tr>
                        <tr>
                            <td>
                              <div align="center">
                                 <input type="hidden" name="digital_objects[{DIR_NUM}][fs_name]"  value="{FS_NAME}" />
                                 <input type="hidden" name="digital_objects[{DIR_NUM}][filesize]" value="0" />
                                 <input type="hidden" name="digital_objects[{DIR_NUM}][is_dir]"   value="1" />
                                 <input type="hidden" name="digital_objects[{DIR_NUM}][inputs_crc]" value="{INPUTS_CRC}" />
                                 <input type="text" name="digital_objects[{DIR_NUM}][prodname]" value="{DIR_PROD_NAME}" size="35" />
                              </div>
                            </td>
                            <td>
                              <div align="center"><textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}][description]" cols=30 rows=3>{DIR_PROD_DESCRIPTION}</textarea></div>
                            </td>
                            <td>
                              <div align="center">
                                 <textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}][image_urls]" cols=30 rows=3>{IMAGE_URLS}</textarea>
                              </div>
                            </td>
                            <td>
                              <div align="center">
                                 <textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}][image_urls_small]" cols=30 rows=3>{IMAGE_URLS_SMALL}</textarea>
                              </div>
                            </td>
                            <td><div align="center"><span style="font-size:8px;"><i>(soon)</i></span></div></td>
                            <td><div align="center"><span style="font-size:8px;"><i>(soon)</i></span></div></td>
                            <td><div align="center">{CURRENCY_SYMBOL}<input type="text" name="digital_objects[{DIR_NUM}][price]" value="{DIR_PRICE}" size="6" /></div></td>
                            <td><div align="center"><textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}][custom_buy_code]" cols=30 rows=3>{DIR_PROD_CUSTOM_BUY_CODE}</textarea></div></td>
                            <td>
                              <div align="center">
                                 <input type="hidden" name="digital_objects[{DIR_NUM}][is_active]" value="0" />
                                 <input type="checkbox" style="float:none;" value="1" name="digital_objects[{DIR_NUM}][is_active]" {CHECKED_IF_ACTIVE} />
                              </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="e_dir_{DIR_NUM}" style="display:none;">
                <td colspan="9">
                  <div align="center">
                     <input style="padding:2px;border:3px solid #3780b3;" type="submit" name="button_update_mwx_settings" value="Save default directory settings" {DISABLED_IF_NOLICENSE} />
                  </div>
                </td>
            </tr>
            <tr class="x_dir_{DIR_NUM}" style="display:none;">
                <td colspan="6">
                  <div id="files_listing_div">
                    <table id="files_listing_table" width="100%">
                        <tr>
                            <th><div align="center"><i>Edit</i></div></th>
                            <th><div align="center">Icon</div></th>
                            <th><div align="center">Filename</div></th>
                            <th><div align="center">File size</div></th>
                            <th><div align="center">Product name</div></th>
                            <th><div align="center">Price</div></th>
                            <th><div align="center">Active?</div></th>
                        </tr>
{FILES_ROWS}
                    </table>
                  </div>
                </td>
            </tr>
TTT;

   $T_file_row=<<<TTT
                        <tr class="tr_visible">
                            <td><div align="center"><div id="e_file_{DIR_NUM}_{FILE_NUM}" class="arrow2"></div></div></td>
                            <td><div align="center"><img src="{FILE_ICON_URL}" height="18" /></div></td>
                            <td><div align="left">{FILE_NAME}</div></td>
                            <td><div align="left">{FILE_SIZE}</div></td>
                            <td><div align="left">{FILE_PROD_NAME}</div></td>
                            <td><div align="center">{CURRENCY_SYMBOL}{FILE_PRICE}</div></td>
                            <td><div align="center">{FILE_IS_ACTIVE}</div></td>
                        </tr>
                        <tr class="e_file_{DIR_NUM}_{FILE_NUM}" style="display:none;">
                            <td colspan="7">
                                <div align="center" style="background-color:#FFC;border:3px solid #e32d2d;padding:5px;margin:5px;">Editing settings for file: <b>{FILE_NAME}</b><br />Enter <b>NULL</b> to inherit default settings.</div>
                                <div align="center">
                                   <table id="file_editing_table" width="100%">
                                       <tr>
                                           <th><div align="center">Product name</div></th>
                                           <th><div align="center">Product description</div></th>
                                           <th><div align="center">Large images URLs</div></th>
                                           <th><div align="center">Small images URLs</div></th>
                                           <th><div align="center">Cat.</div></th>
                                           <th><div align="center">Tags</div></th>
                                           <th><div align="center">Price</div></th>
                                           <th><div align="center">Custom Buy Code</div></th>
                                           <th><div align="center">Active?</div></th>
                                       </tr>
                                       <tr>
                                           <td>
                                             <div align="center">
                                                <input type="hidden" name="digital_objects[{DIR_NUM}_{FILE_NUM}][fs_name]"  value="{FS_NAME}" />
                                                <input type="hidden" name="digital_objects[{DIR_NUM}_{FILE_NUM}][filesize]" value="{FILESIZE}" />
                                                <input type="hidden" name="digital_objects[{DIR_NUM}_{FILE_NUM}][is_dir]"   value="0" />
                                                <input type="hidden" name="digital_objects[{DIR_NUM}_{FILE_NUM}][inputs_crc]" value="{INPUTS_CRC}" />
                                                <input type="text" name="digital_objects[{DIR_NUM}_{FILE_NUM}][prodname]" value="{FILE_PROD_NAME}" size="35" />
                                             </div>
                                           </td>
                                           <td><div align="center"><textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}_{FILE_NUM}][description]" cols=30 rows=3>{FILE_PROD_DESCRIPTION}</textarea></div></td>
                                           <td><div align="center"><textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}_{FILE_NUM}][image_urls]" cols=30 rows=3>{IMAGE_URLS}</textarea></div></td>
                                           <td><div align="center"><textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}_{FILE_NUM}][image_urls_small]" cols=30 rows=3>{IMAGE_URLS_SMALL}</textarea></div></td>
                                           <td><div align="center"><span style="font-size:8px;"><i>(soon)</i></span></div></td>
                                           <td><div align="center"><span style="font-size:8px;"><i>(soon)</i></span></div></td>
                                           <td><div align="center">{CURRENCY_SYMBOL}<input type="text" name="digital_objects[{DIR_NUM}_{FILE_NUM}][price]" value="{FILE_PRICE}" size="6" /></div></td>
                                           <td><div align="center"><textarea style="font-size:10px;" name="digital_objects[{DIR_NUM}_{FILE_NUM}][custom_buy_code]" cols=30 rows=3>{FILE_PROD_CUSTOM_BUY_CODE}</textarea></div></td>
                                           <td>
                                             <div align="center">
                                                <input type="hidden" name="digital_objects[{DIR_NUM}_{FILE_NUM}][is_active]" value="0" />
                                                <input type="checkbox" style="float:none;" value="1" name="digital_objects[{DIR_NUM}_{FILE_NUM}][is_active]" {CHECKED_IF_ACTIVE} />
                                             </div>
                                           </td>
                                       </tr>
                                       <tr class="e_file_{DIR_NUM}_{FILE_NUM}" style="display:none;">
                                           <td colspan="9">
                                             <div align="center">
                                                <input style="padding:2px;border:3px solid #37a864;" type="submit" name="button_update_mwx_settings" value="Save file settings" {DISABLED_IF_NOLICENSE} />
                                             </div>
                                           </td>
                                       </tr>
                                   </table>
                                 </div>
                            </td>
                        </tr>
TTT;

   //---------------------------------------
   if (!MWX__License_Allowed ($mwx_settings))
      {
      $complete_listing_html = str_replace ('{DIRS_ROWS}', '<tr><td colspan="6"><div align="center"><b><span style="color:#800;">Feature Disabled</span></b><br />No Valid <a href="http://www.memberwing.com/#BuyMemberWingNow" target="_blank">MemberWing-X License</a> present</div></td></tr>', $T_complete_listing);
      echo $complete_listing_html;
      return;
      }
   //---------------------------------------

   //---------------------------------------
   // Read list of all protected files
   $all_files            = array();

   // array ('img', 'css', 'js', ); ...
   $skip_dirs_array =                                 explode (',', trim($mwx_settings['unrestricted_access_directory_names']));

   $skip_dirs_array = array_merge ($skip_dirs_array,  explode (',', trim($mwx_settings['locked_access_directory_names'])));
   MWX__enumerate_files_in_dir ($mwx_settings['protected_files_physical_addr'], $all_files, TRUE, $skip_dirs_array);
   $relative_dir_chars = strlen ($mwx_settings['protected_files_physical_addr']);
   // 'PREMIUM_FILES/'.
   $directory_display_prefix = ".../" . preg_replace ('@^.*?/([^/]+$)@', "$1", $mwx_settings['protected_files_physical_addr']) . "/";

   $currency_symbol = MWX__GetCurrencySymbolHTML ($mwx_settings);
   $dirs_rows = "";
   $dir_num   = 1;
   foreach ($all_files as $full_dirname=>$files)
      {
      $dirname = substr ($full_dirname, $relative_dir_chars);
      $dirname = str_replace ('\\', '/', $dirname);
      $dirname = ltrim ($dirname, '/');

      // Construct values for this dir object. Scan all the way to the parent to find values for NULL's.
      $dir_object_values = MWX__Fill_DirObject_Values ($dirname);
      $dir_icon_url = explode ("\n", $dir_object_values['image_urls_small']);
      $dir_icon_url = trim ($dir_icon_url[0]);

      $dir_row = $T_dir_row;
      $dir_row = str_replace ('{DIR_NUM}',               $dir_num,                                                      $dir_row);
      $dir_row = str_replace ('{DIR_PROD_NAME}',         $dir_object_values['prodname'],                                $dir_row);
      $dir_row = str_replace ('{DIR_PROD_DESCRIPTION}',  $dir_object_values['description'],                             $dir_row);
      $dir_row = str_replace ('{DIR_PROD_CUSTOM_BUY_CODE}', $dir_object_values['custom_buy_code'],                      $dir_row);
      $dir_row = str_replace ('{DIR_ICON_URL}',          $dir_icon_url,                                                 $dir_row);
      $dir_row = str_replace ('{DIR_NAME}',              $directory_display_prefix . $dirname . " (" . count($files) . ")", $dir_row);
      $dir_row = str_replace ('{CURRENCY_SYMBOL}',       $currency_symbol,                                              $dir_row);
      $dir_row = str_replace ('{DIR_PRICE}',             $dir_object_values['price'],                                   $dir_row);
      $dir_row = str_replace ('{DIR_IS_ACTIVE}',         $dir_object_values['is_active']?"Yes":"No",                    $dir_row);
      $dir_row = str_replace ('{FS_NAME}',               $dirname,                                                      $dir_row);
      $dir_row = str_replace ('{IMAGE_URLS}',            $dir_object_values['image_urls'],                              $dir_row);
      $dir_row = str_replace ('{IMAGE_URLS_SMALL}',      $dir_object_values['image_urls_small'],                        $dir_row);
      $dir_row = str_replace ('{CHECKED_IF_ACTIVE}',     $dir_object_values['is_active']?'checked="checked"':'',        $dir_row);
      $dir_row = str_replace ('{INPUTS_CRC}',            MWX__GetDigitalObjectValues_CRC($dir_object_values),           $dir_row);
      $dir_row = str_replace ('{DISABLED_IF_NOLICENSE}', MWX__License_Allowed ($mwx_settings)?'':'disabled="disabled"',   $dir_row);

      $files_rows = "";
      $file_num   = 1;
      foreach ($files as $file)
         {
         // Get values for this file object.
         // WARNING: PHP is silly enough in 2010 to return filesize as signed integer. Hence correct filesize will only work for files <2GB.
         //          We can still use it as is - we only need it to be reasonably unique value to ID file.
         $filesize = MWX__filesize ($full_dirname . '/' . $file);

         $file_object_values = MWX__Fill_FileObject_Values ($dirname, array('fs_name'=>$file, 'filesize'=>$filesize, 'is_dir'=>FALSE), $dir_object_values);
         $file_icon_url = explode ('\n', $file_object_values['image_urls_small']);
         $file_icon_url = trim ($file_icon_url[0]);

         $file_row = $T_file_row;
         $file_row = str_replace ('{DIR_NUM}',                 $dir_num,                                                      $file_row);
         $file_row = str_replace ('{FILE_NUM}',                $file_num,                                                     $file_row);
         $file_row = str_replace ('{FILE_ICON_URL}',           $file_icon_url,                                                $file_row);
         $file_row = str_replace ('{FILE_NAME}',               $file,                                                         $file_row);
         $file_row = str_replace ('{FILE_SIZE}',               $filesize,                                                     $file_row);
         $file_row = str_replace ('{FILE_PROD_NAME}',          $file_object_values['prodname'],                               $file_row);
         $file_row = str_replace ('{CURRENCY_SYMBOL}',         $currency_symbol,                                              $file_row);
         $file_row = str_replace ('{FILE_PRICE}',              $file_object_values['price'],                                  $file_row);
         $file_row = str_replace ('{FILE_IS_ACTIVE}',          $file_object_values['is_active']?"Yes":"No",                   $file_row);
         $file_row = str_replace ('{FS_NAME}',                 $file,                                                         $file_row);
         $file_row = str_replace ('{FILESIZE}',                $filesize,                                                     $file_row);
         $file_row = str_replace ('{INPUTS_CRC}',              MWX__GetDigitalObjectValues_CRC($file_object_values),          $file_row);
         $file_row = str_replace ('{FILE_PROD_DESCRIPTION}',   $file_object_values['description'],                            $file_row);
         $file_row = str_replace ('{FILE_PROD_CUSTOM_BUY_CODE}', $file_object_values['custom_buy_code'],                      $file_row);
         $file_row = str_replace ('{IMAGE_URLS}',              $file_object_values['image_urls'],                             $file_row);
         $file_row = str_replace ('{IMAGE_URLS_SMALL}',        $file_object_values['image_urls_small'],                       $file_row);
         $file_row = str_replace ('{CHECKED_IF_ACTIVE}',       $file_object_values['is_active']?'checked="checked"':'',       $file_row);
         $file_row = str_replace ('{DISABLED_IF_NOLICENSE}',   MWX__License_Allowed ($mwx_settings)?'':'disabled="disabled"',   $file_row);

         $files_rows .= $file_row;
         $file_num++;
         }

      $dir_row    = str_replace ('{FILES_ROWS}', $files_rows, $dir_row);
      $dirs_rows .= $dir_row;

      $dir_num++;
      }

   // This causes Out of Memory errors with some lousy hosting companies:
   //    $complete_listing_html = str_replace ('{DIRS_ROWS}', $dirs_rows, $T_complete_listing);
   // This is fix:
   $complete_listing_html = explode ('{DIRS_ROWS}', $T_complete_listing);

   echo $complete_listing_html[0];
   echo $dirs_rows;
   echo $complete_listing_html[1];

}
//===========================================================================

//===========================================================================
function MWX__render_dos_settings_page_html_Instructions ()
{
   $mwx_settings = MWX__get_settings ();
?>

<div align="center" style="border:1px solid gray;background-color:#FFD;margin:10px 0;">There are multiple ways to insert digital online store into your site and even replicate it on any number of other websites or blogs:</div>
<ol>
  <li><b>Method 1</b>: Insert this tag into any post or page on your site to display all products in your digital online store:<br />
    <code>[mwx-digital-online-store]</code> - this will generate digital online store using template 't1'<br />
      <code>[mwx-digital-online-store t3]</code> - this will generate digital online store using template 't3'<br />
  </li>
  <li><b>Method 2</b>: Copy embeddable digital online store widget code (see above "Your Online Store Widget") and paste it into any page of any website that supports javascript. This widget will convert into online store automatically.<br />
  Embeddable digital online store widget is a great way to create multiple copies of your online store all over the web very quickly.<br />Within widget's HTML code you may customize variables: <b>use_template</b>, <b>regex_include</b>, <b>regex_exclude</b> and <b>max_items</b> to customize the way online store will show up.
  </li>
  <li><b>Method 3</b>: Use embedded PHP code snippet to generate more customized version of digital online store. To do that:
    <ul>
      <li>Insert this tag inside of any post/page:<br />
        <code>{{MWXPHP=online_store1}}</code></li>
      <li>Create custom field named online_store1 with the following content.<br /><u>Example 3.1:</u><br />
         <code>if (function_exists('MWX__digital_online_store')) {echo MWX__digital_online_store(array('use_template'=&gt;'t5', 'max_items'=&gt;8));}</code><br />
         Above snippet and custom field will generate digital online store, using template 't5' showing maximum of 8 items for sale.<br /><u>Example 3.2:</u><br />
         <code>if (function_exists('MWX__digital_online_store')) {echo MWX__digital_online_store(array('use_template'=&gt;'t1', 'max_items'=&gt;10, 'regex_include'=&gt;'@\.pdf$@i', 'regex_exclude'=&gt;'@sample@', ));}</code><br />
         Above snippet and custom field will generate digital online store, using template 't1' showing maximum of 10 items for sale matching *.pdf or *.PDF specification. The list will exclude any files that contain keyword 'sample' in it's name.<br />
      </li>
    </ul>
  </li>
  <li><b>Method 4</b>: Using PHP API call in this format:<br />
    <code>&lt;?php<br />
    echo file_get_contents('<?php echo $mwx_settings['dos_api_endpoint']; ?>?format=html&amp;use_template=t1&amp;max_items=5');<br />
    ?&gt;</code><br />
    Above API call will pull complete HTML content for digital online store, using template t1 and will show up to 5 items.</li>
</ol>
<?php
}
//===========================================================================
?>