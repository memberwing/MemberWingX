<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Your Membership Website. Subscribe/Join Page Instructions</title>
<style type="text/css">
<!--
.text {
   font-family: Georgia, Times New Roman, Times, serif;
   margin: 50px;
   padding: 50px;
   border: 4px solid gray;
}
.h1custom {
   font-size: large;
}
-->
</style>
</head>

<body>
<div class="text">
<h1 align="center"><a href="http://www.memberwing.com/" class="h1custom">Wordpress Membership Plugin</a> <a href="http://www.memberwing.com/x/" class="h1custom">MemberWing-X</a></h1>
<div align="center"><p align="center" style="padding:10px;margin:10px 100px;border:1px solid red;background-color:#FFD;"><strong>It seems that you have not created your Subscribe/Join page yet!</strong><br />
No worries, it is easy. To do that:</p></div>
<ol>
  <li>Please login to your <a href="<?php echo get_bloginfo ('wpurl') . '/wp-admin'; ?>" target="_blank">Wordpress Admin panel</a> </li>
  <li>Navigate to Pages-&gt;Add new</li>
  <li>Name it 'Subscribe'</li>
  <li>Within this page explain your membership benefits and paste the code of your Paypal 'Subscribe' button so visitors may click it, pay and join your membership site</li>
  <li>To create your 'Subscribe' button code - please login to your Paypal account, navigate to &quot;Merchant Services&quot; tab and you'll be presented with Paypal buttons creation wizard.</li>
  <li>Note: if you still getting this error OR named your 'Subscribe' page differently, such as 'Join' or some other name - you will need to update MemberWing-X settings like this:
    <ol>
      <li>In your Wordpress administration screen navigate to lower left column of your admin screen over to: MemberWingX-&gt;General Settings </li>
      <li>Find setting named {SUBSCRIBE_URL_PREMIUM}</li>
      <li>Set URL of your subscribe page to the one that you've created.</li>
      <li>Press [Save Settings] button below</li>
    </ol>
  </li>
  <li>You're done!<br />
    <br />
  </li>
  <li><strong>...Oh-oh... sounds like a rocket science?</strong> <u><strong>No worries</strong></u>!<br />
    MemberWing offer consulting services and one of our packages 
  includes complete MemberWing software installation and configuration service to help you get up and running with your online business in no time!<br />
  If you want we could even register any domain for you on your name, install wordpress for you, completely configure it, optimize it for top search engine friendliness, install and configure all necessary plugins + install and configure MemberWing. From zero to hero!<br />
  Please <a href="http://www.memberwing.com/contact/"><strong>contact us</strong></a> at once 
  and we'll be happy to give you a quote and assist you.</li>
</ol>
<div><a href="http://www.memberwing.com/">MemberWing</a> team</div>
</div>
</body>
</html>