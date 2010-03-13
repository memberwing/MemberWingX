<?php

/* **************************************************************************

   MWX Integrated Affiliate Network helpers

This software is provided "as is" without any express or implied warranties,
including, but not limited to, the implied warranties of merchantibility and
fitness for any purpose.
In no event shall the copyright owner, website owner or contributors be liable
for any direct, indirect, incidental, special, exemplary, or consequential
damages (including, but not limited to, procurement of substitute goods or services;
loss of use, data, rankings with any search engines, any penalties for usage of
this software or loss of profits; or business interruption) however caused and
on any theory of liability, whether in contract, strict liability, or
tort(including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.
To request source code for MemberWing please contact http://www.memberwing.com/contact
************************************************************************** */
/*
      These functions are called from outside of wordpress, but they want to use Wordpress API's
      Make sure Wordpress is initialized here.
*/

//===========================================================================
// Save/Retrieve ID of currently active referrer (originally taken from either $_GET['aff'] or cookie)
// Note: "raw" ID could be either email or ID.
global $g_MWX__current_affiliate_id;
$g_MWX__current_affiliate_id = 0;

function MWX__SetCurrentAffiliateRawID ($affid)
{
   global $g_MWX__current_affiliate_id;
   $g_MWX__current_affiliate_id = $affid;
}

function MWX__GetCurrentAffiliateRawID ()
{
   global $g_MWX__current_affiliate_id;
   return $g_MWX__current_affiliate_id;
}
//===========================================================================

//===========================================================================
// '$aff_raw_id' variations:
//    24 - direct ID of immediate affiliate
//    john@smith.com - email of affiliate
//    25,15     - first and second tier affiliates for given visitor. Multiple tier affiliate ID's may be present and separated by comma
//    25,15,373 - first, second and third tier affiliates for given visitor
//
// Convert raw affiliate id (which could be either email or ID) to information array about this affiliate:
// Returns:
//    array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
//    FALSE - if totally invalid id was specified.

function MWX__GetAffiliateInfoByRawID ($aff_raw_id)
{
   $aff_id       = 0;
   $aff_email    = "";
   $aff_username = "";

   $aff_raw_id = explode (',', $aff_raw_id);
   $aff_raw_id = $aff_raw_id[0]; // Pull first tier affiliate (main affiliate).

   // Get valid affiliate ID first
   if (strpos ($aff_raw_id, '@'))
      {
      $aff_email = $aff_raw_id;
      $aff_id    = email_exists ($aff_email);
      // ID maybe false here if affiliate is not yet a member of blog
      }
   else
      {
      // $aff_raw_id - is not email. See if it is valid ID.
      $aff_id = $aff_raw_id;
      }

   if ($aff_id)
      {
      $aff_data = get_userdata ($aff_id);
      if (is_object($aff_data))
         {
         $aff_email    = $aff_data->user_email;
         $aff_username = $aff_data->user_login;
         }
      }

   // If we are here - we got affiliate's email and possibly ID ready
   $aff_info = array();
   $aff_info['aff_id']        = $aff_id;
   $aff_info['aff_email']     = $aff_email;
   $aff_info['aff_username']  = $aff_username;
   $aff_info['aff_password']  = "";             // For compatibility with MWX__CreateAffiliate...
   if ($aff_id)
      {
      $aff_info['mwx_aff_info']  = maybe_unserialize(get_usermeta ($aff_id, 'mwx_aff_info'));
      if (!is_array($aff_info['mwx_aff_info']) || !count($aff_info['mwx_aff_info']))
         $aff_info['mwx_aff_info']  = FALSE;
      }
   else
      $aff_info['mwx_aff_info']  = FALSE;

   return ($aff_info);
}
//===========================================================================

//===========================================================================
// Checks if current affiliate is due for the payout with the current sale.
// If yes - returns TRUE.
// If not - returns FALSE
function MWX__CurrentAffiliateInstantlyPayable ($current_total_sale)
{
   $payout_for_this_sale = 0;

   $mwx_settings = MWX__get_settings ();

   if (!$mwx_settings['mwx_affiliate_network_enabled'])
      return FALSE;  // No integrated affiliate network is enabled.

   $current_aff_raw_id = MWX__GetCurrentAffiliateRawID();
   if (!$current_aff_raw_id || $current_aff_raw_id == 'self')
      return FALSE;  // No affiliate referral for this visit detected.

   //    array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
   $aff_info = MWX__GetAffiliateInfoByRawID ($current_aff_raw_id);
   if (isset($aff_info['aff_email']))
      $aff_email = $aff_info['aff_email'];
   else
      $aff_email = FALSE;

   if (!$aff_email)
      return FALSE;  // Cannot detect aff email addr.

   if ($mwx_settings['aff_manual_aff_sale_approval'] || $mwx_settings['aff_manual_payouts'] || $mwx_settings['aff_sale_auto_approve_in_days'])
      return FALSE;  // If manual or date limit is set - then current sale never auto-paid even if he reached min payment threshold in previous sales.

   if (isset($aff_info['mwx_aff_info']) && is_array($aff_info['mwx_aff_info']))
      $mwx_aff_info = $aff_info['mwx_aff_info'];
   else
      $mwx_aff_info = FALSE;

   if (is_array($mwx_aff_info) && $mwx_aff_info['aff_status'] != 'active')
      return FALSE;  // If affiliate exists - he must have an 'active affiliate' status with the blog.

   if (!is_array($mwx_aff_info) && !$mwx_settings['aff_auto_approve_affiliates'])
      return FALSE;  // If affiliate not exists - auto_approve must be true

   if (is_array($mwx_aff_info))
      {
      // Here we will check affiliate's personal referral record to see if he qualified to be paid this time...
      // Calculate his total accumulated payments due to see if he qualifies to be paid now.

      // Returns: array ('aff_id'=>123, 'aff_email'=>'', 'due_payout_amt'=>'123.45');
      $due_payout_data = MWX__CalculateDuePayoutForAffiliate ($mwx_settings, $current_aff_raw_id);
      $due_payout_amt = $due_payout_data['due_payout_amt']; // Note: even if affiliate has positive balance - but has not yet exceeded minimum payout threshold - it will return "0" for $due_payout_amt

      // Calculate payout for this sale for already existing affiliate
      $payout_for_this_sale = MWX__CalculateAffiliatePayoutForSale ($current_aff_raw_id, $current_total_sale, $mwx_aff_info, $mwx_settings, 1);

      $total_outstanding_due = $payout_for_this_sale + $due_payout_amt;

      if ($total_outstanding_due <= 0)
         return FALSE;

      if (!$mwx_settings['aff_min_payout_threshold'] || $mwx_aff_info['immune_to_min_payout_limit'])
         return TRUE;   // Green light. No min payout set for blog or affiliate is immune to min payout limits.

      if ($total_outstanding_due >= $mwx_settings['aff_min_payout_threshold'])
         return TRUE; // This affiliate have reached payment threshold with this sale + previous referred sales..
      }
   else
      {
      // Calculate payout for this sale for not yet existing affiliate
      $payout_for_this_sale = MWX__CalculateAffiliatePayoutForSale ($current_aff_raw_id, $current_total_sale, $mwx_aff_info, $mwx_settings, 1);
      $total_outstanding_due = $payout_for_this_sale;

      if (!$mwx_settings['aff_min_payout_threshold'])
         return TRUE;   // Green light. No min payout set for blog
      if ($total_outstanding_due >= $mwx_settings['aff_min_payout_threshold'])
         return TRUE;   // This affiliate have reached payment threshold with this sale
      }

   return FALSE;
}
//===========================================================================

//===========================================================================
function MWX__CalculateAffiliatePayoutForSale ($affiliate_raw_id, $total_sale_amt, $mwx_aff_info=FALSE, $mwx_settings=FALSE, $tier=1)
{
   if (!$mwx_settings)
      $mwx_settings = MWX__get_settings ();

   if (!$mwx_aff_info)
      {
      $aff_info = MWX__GetAffiliateInfoByRawID ($affiliate_raw_id);

      if (isset($aff_info['mwx_aff_info']))
         $mwx_aff_info = $aff_info['mwx_aff_info'];
      else
         $mwx_aff_info = FALSE;
      }

   // Calculate payout for this sale for already existing affiliate
   if ($tier==0 || $tier==1)
      {
      $pcts = max($mwx_settings['aff_payout_percents'], @$mwx_aff_info['payout_percents']);
      }
   else if ($tier==2)
      $pcts = $mwx_settings['aff_payout_percents2'];
   else
      // More tiers are possible - but their payout percentage will be the same as third tier
      $pcts = $mwx_settings['aff_payout_percents3'];

   $payout_for_this_sale = $total_sale_amt * $pcts / 100;

   // "1.2345" -> "1.23"
   $payout_for_this_sale = round ($payout_for_this_sale, 2);

   return ($payout_for_this_sale);
}
//===========================================================================

//===========================================================================
// '$notify_new_affiliate' = true => notification email will be sent to new affiliate if affiliate did not exist yet.
// Return ID of newly added user
// Success:
//    -  Returns array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
// Error:
//    -  Returns string with error description.

function MWX__CreateNewAffiliate ($email, $desired_username, $desired_password, $sandbox_account, $notify_new_affiliate)
{
   if (!$email)
      return ("Error: Invalid request: email missing");

   $email = str_replace (' ', '+', $email);

   $new_affiliate=  FALSE;

   $mwx_settings = MWX__get_settings ();

   $new_aff_info = array();

   // Create new default metadata for new affiliate
   $aff_meta = array (
      'aff_status'=>$mwx_settings['aff_auto_approve_affiliates']?'active':'pending', // active, pending, declined, banned.
      'immune_to_min_payout_limit'=>'0',
      'payout_percents'=>'0',    // 0=> use system default
      'payout_adjustment'=>'0',  // Outstanding bonus (+) or outstanding payment adjustment (-)  (product refund for already paid commission)
      'sandbox_account'=>$sandbox_account,
      'payouts'   => array(),          // array( array('date'=>'', 'payout_txn_id'=>'', 'payout_amt'=>'', 'payout_adjustment_included'=>''), array(...))
      'referrals' => array(),
         // array (  Each referral sale recorded here:
         //    'txn_date'        => $_inputs['U_txn_date'],
         //    'txn_id'          => $_inputs['txn_id'],
         //    'full_sale_amt'   => $_inputs['mc_amount3_gross'],
         //    'payout_amt'      => MWX__CalculateAffiliatePayoutForSale(...),
         //    'affiliate_tier'  => $tier+1,    // 1-main affiliate, 2...5
         //    'referral_for_id' => $user_id,
         //    'status'          => $aff_txn_status,        // 'approved', 'declined', 'refunded', 'reversed', 'pending', 'adjusted'
         //    'paid'            => $_inputs['aff_paid'],   // Instantly paid? If Adaptive payment => was instantly paid, else:not.
         //    );
      );

   //    array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
   $aff_info = MWX__GetAffiliateInfoByRawID ($email);

   if ($aff_info['aff_id'])
      {
      $new_affiliate = FALSE;
      // User already exists
      $new_aff_info['aff_id']        = $aff_info['aff_id'];
      $new_aff_info['aff_username']  = $aff_info['aff_username'];
      $new_aff_info['aff_password']  = 'Existing-Password';
      $new_aff_info['aff_email']     = $email;
      if (is_array($aff_info['mwx_aff_info']) && count($aff_info['mwx_aff_info']))
         {
      // User already exists and is affiliate
         $new_aff_info['mwx_aff_info']  = $aff_info['mwx_aff_info'];    // This affiliate already exists.
         MWX__log_event (__FILE__, __LINE__, "Warning: MWX__CreateNewAffiliate() called for already existing affiliate: $email. Ignoring...");
         }
      else
         {
         // User already exists but not affiliate yet
         $new_aff_info['mwx_aff_info']  = $aff_meta;

         // Update info for this affiliate
         update_usermeta ($new_aff_info['aff_id'], 'mwx_aff_info', serialize ($aff_meta));
         MWX__log_event (__FILE__, __LINE__, "Note: Added affiliate metadata record for already existing user: $email.");
         }
      }
   else
      {
      $new_affiliate = TRUE;
      // User does not exists yet.
      // Make sure username will be unique
      $i=1;
      $actual_username = $desired_username;
      if (!$actual_username)
         $actual_username = $email;
      while (username_exists($actual_username))
         $actual_username = $desired_username . $i++;

      if (!$desired_password)
         $actual_password = substr(md5(microtime()), -8);  // Generate random 8-chars password.
      else
         $actual_password = $desired_password;

      $new_aff_info['aff_id']        = wp_create_user ($actual_username, $actual_password, $email);
      $new_aff_info['aff_username']  = $actual_username;
      $new_aff_info['aff_password']  = $actual_password;
      $new_aff_info['aff_email']     = $email;
      $new_aff_info['mwx_aff_info']  = $aff_meta;

      // Update info for this affiliate
      update_usermeta ($new_aff_info['aff_id'], 'mwx_aff_info', serialize ($aff_meta));

      MWX__log_event (__FILE__, __LINE__, "Note: Added brand new user-affiliate: $email. L/P: $actual_username/$actual_password.");
      }

   if ($new_affiliate)
      $new_aff_info['new_affiliate'] = '1';  // New affiliate account was created.
   else
      $new_aff_info['new_affiliate'] = '0';  // Account with this email already existed.

   if ($new_affiliate && $notify_new_affiliate)
      {
      // Notifying new affiliate by email
      MWX__send_email (
         $new_aff_info['aff_email'],               // To
         MWX__Get_Admin_Email (),                  // From
         "Welcome to our affiliate program",       // Subject
         "<br />Welcome to our affiliate program!" .
         "<br />Your new affiliate account was automatically created. Here are your login credentials:<br /><br />" .
         "<br />Login URL: " . rtrim(get_bloginfo ('wpurl'), '/') . '/wp-login.php' .
         "<br />Username : " . $new_aff_info['aff_username'] .
         "<br />Password : " . $new_aff_info['aff_password'] .
         "<br /><br />We are looking forward to do business together with you" .
         "<br /><br />   " . get_bloginfo ('wpurl')
         );
      }

   return ($new_aff_info);
}
//===========================================================================

//===========================================================================
// Function check all pending transactions, auto-approves ones that are eligible, calculates total due payout,
// checks whether affiliate is eligible for payout and if answer to all previous is 'YES' - returns total calculated payout value.
// Else - returns 0 or possible negative value (if payout adjustment is negative).
//
// Returns: array ('aff_id'=>123, 'aff_email'=>'', 'due_payout_amt'=>'123.45');

function MWX__CalculateDuePayoutForAffiliate ($mwx_settings, $aff_raw_id)
{
   //    array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
   $aff_info = MWX__GetAffiliateInfoByRawID ($aff_raw_id);
   $return_data = array ('aff_id'=>$aff_info['aff_id'], 'aff_email'=>$aff_info['aff_email'], 'due_payout_amt'=>'0');
   $aff_raw_id = $aff_info['aff_id'];  // Make sure we have real ID

   $aff_info = $aff_info['mwx_aff_info'];

   if (!isset($aff_info['aff_status']) || $aff_info['aff_status'] != 'active')
      {
      return $return_data;
      }

   $aff_info_marked_as_paid = $aff_info;

   $aff_info_updated = FALSE;

   // Iterate through affiliate's referrals...
   if (isset($aff_info['referrals']) && count($aff_info['referrals']))
      {
      foreach ($aff_info['referrals'] as $ref_idx=>$referral)
         {
         if ($referral['paid'])
            continue;   // Skip referrals that were instantly paid.
         else if ($referral['status'] == 'approved')
            {
            $return_data['due_payout_amt'] += $referral['payout_amt'];
            continue;
            }
         else if ($referral['status'] == 'pending')
            {
            // Reset 'pending' to 'approved' if payment deadline passed AND manual-approval is not set.
            if ($mwx_settings['aff_manual_aff_sale_approval'])
               continue;   // Webmaster needs to go to each affiliate's profile and manually approve each sale.
            else if (!$mwx_settings['aff_sale_auto_approve_in_days'])
               {
               $aff_info['referrals'][$ref_idx]['status'] = 'approved';
               $aff_info_updated = TRUE;
               $return_data['due_payout_amt']   += $referral['payout_amt'];
               }
            else
               {
               $referral_age_days = floor((strtotime("now") - strtotime($referral['txn_date'])) / (60*60*24)); ///!!! debug it
               if ($referral_age_days > $mwx_settings['aff_sale_auto_approve_in_days'])
                  {
                  $aff_info['referrals'][$ref_idx]['status'] = 'approved';
                  $aff_info_updated = TRUE;
                  $return_data['due_payout_amt']   += $referral['payout_amt'];
                  }
               }
            }
         }
      }

   if (!isset($aff_info['payouts']))
      {
      $aff_info['payouts'] = array();
      $aff_info_updated = TRUE;
      }

   if ($aff_info_updated)
      {
      update_usermeta ($aff_raw_id, 'mwx_aff_info', serialize ($aff_info));
      }

   // Take outstanding debits/credits into consideration
   $return_data['due_payout_amt'] += $aff_info['payout_adjustment'];

   // Subtract all previous successful 'payouts' from the final number.
   foreach ($aff_info['payouts'] as $payout)
      {
      $return_data['due_payout_amt'] -= $payout['payout_amt'];
      $return_data['due_payout_amt'] += @$payout['payout_adjustment_included'];  // Correction on amount of payout adjustemnt that was absorbed/covered by that payout. (Payout adjustment is not part of referrals but it need to be considered)
      }

   if ($return_data['due_payout_amt'] <= 0)
      {
      return $return_data;
      }

   // Affiliate's due payout is above zero here...
   if ($aff_info['immune_to_min_payout_limit'] || $return_data['due_payout_amt'] > $mwx_settings['aff_min_payout_threshold'])
      {
      return $return_data; // Affiliate is immune to sitewide minimal payout thresholds or reached above threshold value.
      }

   // Affiliate has not climbed above minimal payout threshold yet.
   $return_data['due_payout_amt'] = 0;

   return $return_data;;
}
//===========================================================================

//===========================================================================
// Function adds new entry to affiliate's metadata - 'payouts' array
//   and possibly promotes affiliate to zero minimum payouts state.

// $_inputs['custom']['evt']        == 'aff_payout'
// $_inputs['custom']['aff_id']     == affiliate's ID
// $_inputs['custom']['aff_payout'] == amount successfully paid to affiliate
//
function MWX__Manual_Affiliate_Payout ()
{
   global   $_inputs;

   $mwx_settings  = MWX__get_settings ();

   //---------------------------------------
   // Pull metadata for affiliate that just been paid
   // array ('aff_id'=>123, 'aff_email'=>'john@smith.com', 'aff_username'=>"...", 'aff_password'=>"...", 'mwx_aff_info'=>"...") - only 'aff_email' is guaranteed to be initialized.
   $aff_info      = MWX__GetAffiliateInfoByRawID ($_inputs['custom']['aff_id']);
   $aff_username  = $aff_info['aff_username'];
   $aff_email     = $aff_info['aff_email'];
   $aff_info      = $aff_info['mwx_aff_info'];
   //---------------------------------------

   // Add new entry into 'payouts' array
   if (!isset($aff_info['payouts']))
      $aff_info['payouts'] = array();

   $aff_info['payouts'][] =
      array (
         'date'                        => $_inputs['U_txn_date'],
         'payout_txn_id'               => $_inputs['txn_id'],
         'payout_amt'                  => $_inputs['custom']['aff_payout'],
         'payout_adjustment_included'  => $aff_info['payout_adjustment'],
         );

   // Reset payout adjustment to zero. All debits/credits are cleared with manual payout event. Otherwise no manual payout would have happened.
   $aff_info['payout_adjustment'] = "0";

   // Check if we can promote this affiliate to zero minimal payouts
   if ($mwx_settings['aff_promotion_to_zero_min_payout'] && !$aff_info['immune_to_min_payout_limit'])
      {
      MWX__log_event (__FILE__, __LINE__, "Note: Affiliate: $aff_username ($aff_email) is promoted to zero minimal payouts");
      $aff_info['immune_to_min_payout_limit'] = '1';
      }

   // Update info for this affiliate
   update_usermeta ($_inputs['custom']['aff_id'], 'mwx_aff_info', serialize ($aff_info));

   MWX__log_event (__FILE__, __LINE__, "Note: Processed manual payment of \${$_inputs['custom']['aff_payout']} for affiliate: $aff_username ($aff_email).");
}
//===========================================================================


?>