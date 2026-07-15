<?php

/*

      Payment Processing script for PlugnPay system

*/

   include_once (dirname(__FILE__) . '/mwx-include-all.php');     // This will load Wordpress

   MWX__log_event (__FILE__, __LINE__, "Raw Entry Hit. ====================== _REQUEST data: =============================\n" . serialize($_REQUEST));

   global $_inputs;
   $_inputs = array ();
   MWX__ResetInputs ($_inputs);

   //---------------------------------------
   // Prevent loading of file directly.
   if (!isset($_POST) || !count($_POST))
      {
      exit ('Cannot call this file directly');
      }
   //---------------------------------------

   //---------------------------------------
   // Security check. Make sure request came from proper server.
   if (strpos($_SERVER['REMOTE_ADDR'], '69.18.') === FALSE)
      {
      MWX__log_event (__FILE__, __LINE__, "ERROR: Request received from bad IP='{$_SERVER['REMOTE_ADDR']}'. Expected: '69.18.*.*'. Fraud attempt? Aborted.");

      // Abort.
      exit();
      }
   //---------------------------------------

   //---------------------------------------
   // Integration must be enabled
   $mwx_settings = MWX__get_settings ();
   if (!$mwx_settings['pnp_integration_enabled'])
      {
      MWX__log_event (__FILE__, __LINE__, "PlugNPay integration is disabled. Processing aborted. Please enable it via MemberWingX->Integration With Other Systems.");
      exit ();
      }
   //---------------------------------------

   //---------------------------------------
   // Gracefully handle 'refresh' and other non-'new' events...
   if (@$_POST['mode'] == 'refresh') // 'refresh'
      {
      MWX__log_event (__FILE__, __LINE__, "Note: processing mode = refresh ...");
      MWX__PNP__ProcessPasswordRefresh ($mwx_settings);
      exit("<html><body>PnP password refresh processed.<br />See: " . dirname(__FILE__).'/__log.php' . " for more details<br /><br />Done.</body></html>");
      }
   //---------------------------------------

   //---------------------------------------
   // Gracefully handle 'refresh' and other non-'new' events...
   if (@$_POST['mode'] != 'new') // 'refresh'
      {
      header("HTTP/1.0 200 OK");
      exit("<html><body>Processed.</body></html>");
      }
   //---------------------------------------

   //---------------------------------------
   // Sanity checks for PnP account
   if (!@$_POST['email'])
      {
      $msg = "PlugNPay ERROR: 'email' field is not present in remote notification coming from PnP side. Aborted.";
      MWX__log_event (__FILE__, __LINE__, $msg);

      // Abort.
      exit("<html><body>{$msg}</body></html>");
      }
   if (!@$_POST['username'])
      {
      $msg = "PlugNPay ERROR: 'username' field is not present in remote notification coming from PnP side. Aborted.";
      MWX__log_event (__FILE__, __LINE__, $msg);

      // Abort.
      exit("<html><body>{$msg}</body></html>");
      }
   if (!@$_POST['password'])
      {
      $msg = "PlugNPay ERROR: 'password' field is not present in remote notification coming from PnP side. Aborted.";
      MWX__log_event (__FILE__, __LINE__, $msg);

      // Abort.
      exit("<html><body>{$msg}</body></html>");
      }
   //---------------------------------------

   //---------------------------------------
   // Assemble proper variables into '$_inputs' array
   //
   $_inputs['item_name']        = 'Premium Membership';  // PnP uncapable of sending product name, so generate default
   $_inputs['item_number']      = '0';
   $_inputs['first_name']       = '';
   $_inputs['last_name']        = '';
   $_inputs['payer_email']      = $_POST['email'];

   $_inputs['desired_username'] = $_POST['username'];
   $_inputs['desired_password'] = $_POST['password'];

   $_inputs['payer_id']         = "0";
   $_inputs['subscr_id']        = substr(md5(microtime()), -8);   // Just randomly generated unique.
   $_inputs['subscr_date']      = date ('Y-m-d H:i:s T');
   $_inputs['U_txn_date']       = $_inputs['subscr_date'];
   $_inputs['recurring']        = '1';
   $_inputs['period3']          = "";
   $_inputs['mc_amount3_gross'] = '0.01'; // Unknown but let make it >0
   $_inputs['mc_currency']      = 'USD';
   $_inputs['txn_id']           = '0';
   $_inputs['txn_type']         = 'subscr_signup';
   $_inputs['payment_status']   = 'completed'; // Force it this way to pass Transaction_Type_Switch
   $_inputs['receiver_email']   = 'Seller email unspecified';
   $_inputs['customer_ip']      = '0.0.0.0';
   //---------------------------------------

   MWX__TransactionTypeSwitch ();
   MWX__log_event (__FILE__, __LINE__, "Done. ==========================================");

echo <<<TTT
<div align="center" style="background-color:#ffe;padding:50px;margin:50px;border:2px solid gray;font-size:1.2em;width:400px;">
   Thank you!<br />
   You will receive notification by email soon.<br /><br />
   Please contact our support team if you will not receive confirmation email within 1 hour.
</div>
TTT;

//===========================================================================
//
//

function MWX__PNP__ProcessPasswordRefresh ($mwx_settings)
{
   global $wpdb;

   global $_inputs;

   //------------------------------------------------------------------------
   // $mwx_settings['pnp_password_file_dir'] . '/pnppasswd.txt';
   //    john3a:johnsmit:20111230:0:glebesman3js3@gmail.com
   //    john4:john4p:20120101:0:glebesmanjs4@gmail.com
   //    john5a:5ajohn:20120101:0:glebesman@gmail.com

   // Get list of currently active users from PlugNPay passwords file

   $passwords_filename = rtrim($mwx_settings['pnp_password_file_dir'], '/') . '/pnppasswd.txt';
   $arr = @file ($passwords_filename, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
   $file_clean = true;

   $pnp_active_members = array();
   $pnp_active_members_data = array();
   if (is_array($arr) && count($arr))
      {
      foreach ($arr as $entry)
         {
         $values = explode (':', $entry);
         if (count($values) != 5)
            {
            MWX__log_event (__FILE__, __LINE__, "Warning: Detected invalid line in PnP passwords file (was expected exactly 5 elements): {$entry}. Processing aborted. Please contact PnP support to fix format of 'pnppasswd.txt' file. Each line must be like this: 'johnsmith:PassWoRd:20120101:0:john@email.com'");
            $file_clean = false;    // Abort on first unclean line
            break;
            }
         $pnp_active_members[] = $values[0];
         $pnp_active_members_data[$values[0]] = array (
            'email'    => @$values[4],
            'username' => @$values[0],
            'password' => @$values[1],
            );
         }
      }
   else
      {
      MWX__log_event (__FILE__, __LINE__, "Warning: PnP passwords file is missing or empty. Password 'refresh' command processing skipped.");
      return;
      }

   if (!$file_clean)
      {
      return;
      }

   /// Uncomment this line to delete passwords file here for better security.
   ///@unlink ($passwords_filename);   // Delete it immediately for security reasons.
   //------------------------------------------------------------------------

   $rows = $wpdb->get_results ("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY ID ASC", ARRAY_A);
   if ($rows === FALSE)
      {
      MWX__log_event (__FILE__, __LINE__, "WARNING: cannot open wordpress database! Processing aborted.");
      }
   else
      {
      MWX__log_event (__FILE__, __LINE__, "Note: 'refresh' processing initiated with " . count($pnp_active_members) . " members listed in passwords file...");

      if (is_array($pnp_active_members) && count($pnp_active_members))
         {
         // Get list of MWX products and currently active members who owns these products.
         // array('Gold membership'=>array('john', 'mary',),  ... );
         //
         $mwx_members_ids               = array();   // array ('john'=>5, 'mary'=>6);  // All existing members at WP/MWX
         $mwx_active_members_products   = array();   // array ('Gold membership'=>array('john', 'mary',),  ... ); Members who has active product[s] on their name.
         $mwx_inactive_members_products = array();   // array ('Gold membership'=>array('john', 'mary',),  ... ); Members who has inactive product[s] on their name.
         $mwx_emptyhanded_members       = array();   // array ('john', 'mary',); Members who does not have any product[s] on their names.

         $all_users = array();
         foreach ($rows as $row)
            {
            $user_data = get_userdata ($row['ID']);
            $mwx_members_ids[$user_data->user_login] = $user_data->ID;

            $mwx_purchases = MWX__GetListOfProductsForUser ($user_data->ID);

            if (is_array($mwx_purchases) && count($mwx_purchases))
               {
               foreach ($mwx_purchases as $mwx_purchase)
                  {
                  if ($mwx_purchase['product_name'])
                     {
                     // Member has some product on his name
                     if (MWX__is_product_active($mwx_purchase['product_status']))
                        {
                        // Member has ACTIVE product on his name
                        if (!isset($mwx_active_members_products[$mwx_purchase['product_name']]))
                           $mwx_active_members_products[$mwx_purchase['product_name']] = array();
                        $mwx_active_members_products[$mwx_purchase['product_name']][] = $user_data->user_login;
                        }
                     else
                        {
                        // Member has INACTIVE product on his name
                        if (!isset($mwx_inactive_members_products[$mwx_purchase['product_name']]))
                           $mwx_inactive_members_products[$mwx_purchase['product_name']] = array();
                        $mwx_inactive_members_products[$mwx_purchase['product_name']][] = $user_data->user_login;
                        }
                     }
                  else
                     {
                     $mwx_emptyhanded_members[] = $user_data->user_login;
                     }
                  }
               }
            else
               {
               // Member does not have 'mwx-purchases' metadata. Was not added by MWX?
               $mwx_emptyhanded_members[] = $user_data->user_login;
               }
            }

         // Process product: 'Premium Membership'
         $product_name = 'Premium Membership';  // Standard product name for all PnP subscriptions.

         //---------------------------------------------------------------------
         // Now make sure that active premium members in MWX/WP are actually still active at PnP side.
         // If not - deactivate them
         if (isset($mwx_active_members_products[$product_name]) && is_array($mwx_active_members_products[$product_name]))
            {
            foreach ($mwx_active_members_products[$product_name] as $currently_active_username)
               {
               if (!in_array($currently_active_username, $pnp_active_members))
                  {
                  // Still active wordpress/MWX premium member is no longer listed as paying member in PnP password file.
                  // Deactivate him!
                  $user_id = $mwx_members_ids[$currently_active_username];
                  $mwx_purchases = MWX__GetListOfProductsForUser ($user_id);

                  if (is_array($mwx_purchases))
                     {
                     foreach ($mwx_purchases as $idx=>$mwx_purchase)
                        {
                        if (@$mwx_purchase['product_name'] == $product_name)
                           {
                           $mwx_purchases[$idx]['product_status'] = 'deactivated';
                           update_user_meta ($user_id, 'mwx_purchases', serialize ($mwx_purchases));
                           MWX__log_event (__FILE__, __LINE__, "Note: deactivated '{$product_name}' for user: '{$currently_active_username}' because he is not listed as premium member in PnP passwords file.");
                           break;
                           }
                        }
                     }
                  }
               }
            }
         //---------------------------------------------------------------------

         //---------------------------------------------------------------------
         // Reactivate inactive MWX/WP users that suddenly appeared back in PnP password file (reactivated subscriptions?).
         // NOTE: this will only reactivate members who already had product listed on their name.
         //       If member who never owned this product before will appear in PnP password file - he WILL NOT get "reactivated"
         //
         //       For such member it would be required to "add new product". This is currently not supported by this code.

         if (isset($mwx_inactive_members_products[$product_name]) && is_array($mwx_inactive_members_products[$product_name]))
            {
            foreach ($mwx_inactive_members_products[$product_name] as $currently_inactive_username)
               {
               if (in_array($currently_inactive_username, $pnp_active_members))
                  {
                  // Inactive wordpress/MWX premium member suddenly got listed as paying member in PnP password file.
                  // Reactivate him!
                  $user_id = $mwx_members_ids[$currently_inactive_username];
                  $mwx_purchases = MWX__GetListOfProductsForUser ($user_id);

                  if (is_array($mwx_purchases))
                     {
                     foreach ($mwx_purchases as $idx=>$mwx_purchase)
                        {
                        if (@$mwx_purchase['product_name'] == $product_name)
                           {
                           $mwx_purchases[$idx]['product_status'] = 'active';
                           update_user_meta ($user_id, 'mwx_purchases', serialize ($mwx_purchases));
                           MWX__log_event (__FILE__, __LINE__, "Note: reactivated '{$product_name}' for user: '{$currently_inactive_username}' because he appeared listed as premium member in PnP passwords file.");
                           break;
                           }
                        }
                     }
                  }
               }
            }
         //---------------------------------------------------------------------

         //---------------------------------------------------------------------
         // Add new premium product ownership to *existing* but emptyhanded members

         if (count($mwx_emptyhanded_members))
            {
            foreach ($mwx_emptyhanded_members as $emptyhanded_member)
               {
               if (in_array($emptyhanded_member, $pnp_active_members))
                  {
                  // Emptyhanded wordpress/MWX premium member suddenly got listed as paying member in PnP password file.
                  // Add him premium product!
                  $user_id = $mwx_members_ids[$emptyhanded_member];
                  $products_purchased = array();
                  $products_purchased[] =
                     array (
                        'product_id'      => '0',
                        'product_name'    => $product_name,
                        'purchase_date'   => date ('Y-m-d H:i:s T'),
                        'full_sale_amt'   => '0.01',
                        'expiry_date'     => '',
                        'txn_ids'         => array(),
                        'subscr_id'       => substr(md5(microtime()), -8),
                        'referred_by_id'  => '',   // Affiliate's id (user_id) who refered this purchase
                        'product_status'  => 'active',      // 'active'(customer is in good standing), 'cancelled'(subscription), 'ended'(subscription ended normally), 'expired'(forced expiry date reached), 'refunded'(one of payments was refunded), 'deactivated'(manually set by admin)
                        );

                  update_user_meta ($user_id, 'mwx_purchases', serialize ($products_purchased));
                  MWX__log_event (__FILE__, __LINE__, "Note: added '{$product_name}' for emptyhanded user: '{$emptyhanded_member}' because he appeared listed as premium member in PnP passwords file.");
                  }
               }
            }
         //---------------------------------------------------------------------

         //---------------------------------------------------------------------
         // Create new member account and add new premium product ownership to *non-existing* members that are just appeared to be listed inside PnP passwords file.

         $pnp_metadata = get_option('PNP_Metadata');
         if (!is_array($pnp_metadata))
            {
            $pnp_metadata       = array();
            $pnp_metadata_dirty = true;
            }
         else
            {
            $pnp_metadata_dirty = false;
            }

         foreach ($pnp_active_members as $pnp_active_member)
            {
            if (!array_key_exists($pnp_active_member, $mwx_members_ids))
               {
               // = array ('peter' => array ('email' => 'glebesman+peter@peter.com', 'username'=>'peter', 'password'=>'ppp'), );
               $pnp_active_member_data = @$pnp_active_members_data[$pnp_active_member];
               if (
                  $pnp_active_member_data                &&
                  is_array($pnp_active_member_data)      &&
                  @$pnp_active_member_data['email']      &&
                  @$pnp_active_member_data['username']   &&
                  @$pnp_active_member_data['password']
                  )
                  {
                  MWX__log_event (__FILE__, __LINE__, "Note: adding *brand new* premium user: '{$pnp_active_member}' because he appeared listed as premium member in PnP passwords file.");
                  MWX__ResetInputs ($_inputs);

                  //-----------------------------
                  // Update pnp metadata with new user's record to keep track of possible future changes to his email/password.
                  $pnp_metadata[$pnp_active_member_data['username']] =
                     array (
                        'email'    => $pnp_active_member_data['email'],
                        'password' => get_password_hash ($pnp_active_member_data['password']),
                        );
                  $pnp_metadata_dirty = true;
                  //-----------------------------

                  $_inputs['item_name']        = $product_name;
                  $_inputs['item_number']      = '0';
                  $_inputs['first_name']       = '';
                  $_inputs['last_name']        = '';
                  $_inputs['payer_email']      = $pnp_active_member_data['email'];

                  $_inputs['desired_username'] = $pnp_active_member_data['username'];
                  $_inputs['desired_password'] = $pnp_active_member_data['password'];

                  $_inputs['payer_id']         = "0";
                  $_inputs['subscr_id']        = substr(md5(microtime()), -8);   // Just randomly generated unique.
                  $_inputs['subscr_date']      = date ('Y-m-d H:i:s T');
                  $_inputs['U_txn_date']       = $_inputs['subscr_date'];
                  $_inputs['recurring']        = '1';
                  $_inputs['period3']          = "";
                  $_inputs['mc_amount3_gross'] = '0.01'; // Unknown but let make it >0
                  $_inputs['mc_currency']      = 'USD';
                  $_inputs['txn_id']           = '0';
                  $_inputs['txn_type']         = 'subscr_signup';
                  $_inputs['payment_status']   = 'completed'; // Force it this way to pass Transaction_Type_Switch
                  $_inputs['receiver_email']   = 'Seller email unspecified';
                  $_inputs['customer_ip']      = '0.0.0.0';

                  MWX__TransactionTypeSwitch ();
                  }
               else
                  {
                  if (!@$pnp_active_member_data['email'])
                     MWX__log_event (__FILE__, __LINE__, "Warning: cannot add *new* premium user: '{$pnp_active_member}' because cannot assemble 'pnp_active_member_data'. Bad passwords file format - email is missing (he appeared listed as premium member in PnP passwords file)");
                  else if (!@$pnp_active_member_data['username'])
                     MWX__log_event (__FILE__, __LINE__, "Warning: cannot add *new* premium user: '{$pnp_active_member}' because cannot assemble 'pnp_active_member_data'. Bad passwords file format - username is missing (he appeared listed as premium member in PnP passwords file)");
                  else if (!@$pnp_active_member_data['password'])
                     MWX__log_event (__FILE__, __LINE__, "Warning: cannot add *new* premium user: '{$pnp_active_member}' because cannot assemble 'pnp_active_member_data'. Bad passwords file format - password is missing (he appeared listed as premium member in PnP passwords file)");
                  else
                     MWX__log_event (__FILE__, __LINE__, "Warning: cannot add *new* premium user: '{$pnp_active_member}' because cannot assemble 'pnp_active_member_data'. Bad passwords file format? (he appeared listed as premium member in PnP passwords file)");
                  }
               }
            else
               {
               // Process possible password/email change for each user listed in pnppasswd.txt
               // = array ('peter' => array ('email' => 'glebesman+peter@peter.com', 'username'=>'peter', 'password'=>'ppp'), );
               $pnp_active_member_data = @$pnp_active_members_data[$pnp_active_member];

               if (isset($pnp_metadata[$pnp_active_member_data['username']]))
                  {
                  // Check if user's password/email has been changed
                  $pnp_metadata_for_user = $pnp_metadata[$pnp_active_member_data['username']];
                  if (
                     $pnp_metadata_for_user['email']    != $pnp_active_member_data['email'] ||
                     $pnp_metadata_for_user['password'] != get_password_hash ($pnp_active_member_data['password'])
                     )
                     {
                     // Update wordpress user data, AND his PnP metadata record
                     $user_id = @$mwx_members_ids[$pnp_active_member_data['username']];
                     if ($user_id)
                        {
                        MWX__log_event (__FILE__, __LINE__, "NOTE: Processing email/password change for user: " . $pnp_active_member_data['username'] . '(' . $pnp_active_member_data['email'] . ')');
                        wp_update_user (array ('ID' => $user_id, 'user_pass' => $pnp_active_member_data['password'], 'user_email' => $pnp_active_member_data['email']));
                        }
                     else
                        {
                        // should not be here.
                        }

                     // And finally update his PnP metadata record
                     $pnp_metadata[$pnp_active_member_data['username']] =
                        array (
                           'email'    => $pnp_active_member_data['email'],
                           'password' => get_password_hash ($pnp_active_member_data['password']),
                           );
                     $pnp_metadata_dirty = true;
                     }
                  else
                     {
                     // No email/password changes detected for this user.
                     }
                  }
               else
                  {
                  // Existing user's info is not yet in PnP metadata. Update metadata
                  $pnp_metadata[$pnp_active_member_data['username']] =
                     array (
                        'email'    => $pnp_active_member_data['email'],
                        'password' => get_password_hash ($pnp_active_member_data['password']),
                        );
                  $pnp_metadata_dirty = true;
                  }
               }
            }

         if ($pnp_metadata_dirty)
            {
            update_option ('PNP_Metadata', $pnp_metadata);
            }
         //---------------------------------------------------------------------

         }
      else
         {
         // Shouldn't be here.
         MWX__log_event (__FILE__, __LINE__, "Note: no entries found in PnP passwords file. Processing skipped.");
         }
      }

/*
         $all_users[] = array (
           'ID'                    => $user_data->ID,
           'user_login'            => $user_data->user_login,
           'user_email'            => $user_data->user_email,
           'user_registered'       => $user_data->user_registered,
           'user_activation_key'   => $user_data->user_activation_key,
           'first_name'            => $user_data->first_name,
           'last_name'             => $user_data->last_name,
           'mwx_extra_user_data'   => MWX__get_usermeta_array ($row['ID'], 'mwx_extra_user_data'),
           'mwx_purchases'         => MWX__get_usermeta_array ($row['ID'], 'mwx_purchases'),
           'mwx_aff_info'          => MWX__get_usermeta_array ($row['ID'], 'mwx_aff_info'),
           );

            [mwx_purchases] => Array
                (
                    [0] => Array
                        (
                            [product_id] => 0
                            [product_name] => Premium Membership
                            [purchase_date] => 2011-11-27 15:39:25 UTC
                            [full_sale_amt] => 0.01
                            [expiry_date] =>
                            [txn_ids] => Array
                                (
                                )

                            [subscr_id] => b4da98c5
                            [referred_by_id] => self
                            [product_status] => active
                        )
                )

*/

}
//===========================================================================

//===========================================================================
function get_password_hash ($plaintext_password)
{
   return md5 ($plaintext_password . 'PnP');
}
//===========================================================================
