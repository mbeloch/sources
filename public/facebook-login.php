<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
	<a href="../index.php">Home</a>
<?php
session_start();
define('FB_DIR', '/FB/src/Facebook/');
require __DIR__ . '/FB/autoload.php';	

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

FacebookSession::setDefaultApplication('', '');

$redirect_url = "http://localhost/sources/public/facebook.php";

$helper = new FacebookRedirectLoginHelper($redirect_url, $appId = NULL, $appSecret = NULL);
echo '<a href="' . $helper->getLoginUrl() . '">Login with Facebook</a>';
?>
</body>
</html>