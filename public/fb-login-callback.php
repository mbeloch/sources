<?php
session_start();
require '../vendor/autoload.php';

$fb = new Facebook\Facebook([
    'app_id' => '516525445096951',
    'app_secret' => '',
    'default_graph_version' => 'v2.3',
]);

$helper = $fb->getRedirectLoginHelper();
try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (isset($accessToken)) {
    // Logged in!
    $_SESSION['facebook_access_token'] = (string) $accessToken;
    header('Location: http://localhost/sources/public/facebook-new.php');

    // Now you can redirect to another page and use the
    // access token from $_SESSION['facebook_access_token']
}