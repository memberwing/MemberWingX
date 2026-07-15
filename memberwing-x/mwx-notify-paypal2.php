<?php

// RAP and other scripts may load Paypal processing logic directly.

$skip_postback = TRUE;
include_once (dirname(__FILE__) . '/mwx-notify-paypal.php');

?>