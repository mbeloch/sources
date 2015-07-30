<?php
session_start();

require '../vendor/autoload.php';
require 'facebook-functions.php';

if(!isset($_SESSION['facebook_access_token'])){
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['user_photos'];
    $loginUrl = $helper->getLoginUrl('http://localhost/sources/public/fb-login-callback.php', $permissions);

    //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
    $data = array('loginUrl' => $loginUrl);
    header('Content-Type: application/json');
    echo json_encode($data);

}else {
    $data = array('loginUrl' => 'ok');

    header('Content-Type: application/json');
    echo json_encode($data);
}