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
define('facebook-php-sdk-v4-4.0-dev', '/FB/src/Facebook/');
require __DIR__ . '/FB/autoload.php';	

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;

FacebookSession::setDefaultApplication('516525445096951', '4dc6b5d8acd4d327b9410cb57a4213f4');

$helper = new FacebookRedirectLoginHelper($redirect_url);
try {
    $session = $helper->getSessionFromRedirect();
} catch(FacebookRequestException $ex) {
    // When Facebook returns an error
} catch(\Exception $ex) {
    // When validation fails or other local issues
}
if ($session) {
  // Logged in.
}

$request = new FacebookRequest($session, 'GET', '/me');
$response = $request->execute();
$graphObject = $response->getGraphObject();
?>
</body>
</html>