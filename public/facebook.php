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
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

FacebookSession::setDefaultApplication('516525445096951', '');

$redirect_url = "http://localhost/sources/public/facebook.php";

$helper = new FacebookRedirectLoginHelper($redirect_url, $appId = NULL, $appSecret = NULL);

if (isset($_SESSION['FB']) && ($_SESSION['FB']) == true) {
    if (isset($_SESSION['valid']) && $_SESSION['valid'] == true) {
        $session = new FacebookSession($_SESSION['fb_token']);
    }
}

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

if (!$session){
    $scope = array('user_photos');
    echo '<a href="' . $helper->getLoginUrl($scope) . '">Login with Facebook</a>';
}

function getCoverPhoto($photoId, $session){
    $request = new FacebookRequest(
        $session,
        'GET',
        '/'.$photoId
    );
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $graphObject = $graphObject->getPropertyAsArray('images');
    $pocet = count($graphObject);
    return $graphObject[$pocet-1]->getProperty('source');
}

if($session) {
    // save the session
    $_SESSION['fb_token'] = $session->getToken();
    // create a session using saved token or the new one we generated at login
    $session = new FacebookSession($session->getToken());
    // graph api request for user data
    $request = new FacebookRequest($session, 'GET', '/me');
    $response = $request->execute();
    $graphObject = $response->getGraphObject()->asArray();

    $_SESSION['valid'] = true;
    $_SESSION['timeout'] = time();

    $_SESSION['FB'] = true;

    $_SESSION['usernameFB'] = $graphObject['name'];
    $_SESSION['idFB'] = $graphObject['id'];
    $_SESSION['first_nameFB'] = $graphObject['first_name'];
    $_SESSION['last_nameFB'] = $graphObject['last_name'];
    $_SESSION['genderFB'] = $graphObject['gender'];


    $request = new FacebookRequest(
        $session,
        'GET',
        'me/albums'
    );
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $graphObject = $graphObject->getPropertyAsArray('data');

    foreach ($graphObject as $album) {
        echo $album->getProperty('name');
        $id = $album->getProperty('id');
        echo "<a href='facebook-photos.php?albumId=".$id."'><img src='" . getCoverPhoto($album->getProperty('cover_photo'), $session) . "' /></a>";
        echo '<br/>';
    }


}

?>
</body>
</html>