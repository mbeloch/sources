<?php
session_start();
require '../vendor/autoload.php';
require 'facebook-functions.php';

if(!isset($_SESSION['facebook_access_token'])){
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['user_about_me', 'email', 'user_photos']; // optional
    $loginUrl = $helper->getLoginUrl('http://localhost/sources/public/fb-login-callback.php', $permissions);

    echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
}else {
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

    $photoId = htmlspecialchars($_GET["photoId"]);
    $photoUrl = downloadUrl($fb, $photoId);

    $img = 'fbPic/fb_'.$photoId.'.jpg';
    file_put_contents($img, file_get_contents($photoUrl));

    echo "<script>window.close();</script>";
}


/*

$ch = curl_init('http://example.com/image.php');
$fp = fopen('/my/folder/flower.gif', 'wb');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);
fclose($fp);
*/