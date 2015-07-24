<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
	<a href="../index.php">Home</a>
    <br/>
<?php
session_start();

require '../vendor/autoload.php';
require 'facebook-functions.php';

if(!isset($_SESSION['facebook_access_token'])){
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['user_about_me', 'email', 'user_photos']; // optional
    $loginUrl = $helper->getLoginUrl('http://localhost/sources/public/fb-login-callback.php', $permissions);

    //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
    $data = array('login' => $loginUrl);
    header('Content-Type: application/json');
    echo json_encode($data);

}else {
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    userAlbums($fb);
}


?>
</body>
</html>