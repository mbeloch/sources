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
use Facebook\FacebookRequest;

FacebookSession::setDefaultApplication('516525445096951', '');
$redirect_url = "http://localhost/sources/public/facebook.php";

$helper = new FacebookRedirectLoginHelper($redirect_url, $appId = NULL, $appSecret = NULL);

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

if (isset($_SESSION['FB']) && ($_SESSION['FB']) == true) {
    if (isset($_SESSION['valid']) && $_SESSION['valid'] == true) {
        echo $_SESSION['usernameFB'];
        $albumId = htmlspecialchars($_GET["albumId"]);
        $session = new FacebookSession($_SESSION['fb_token']);

        $request = new FacebookRequest(
            $session,
            'GET',
            '/'.$albumId.'/photos'
        );
        $response = $request->execute();
        $graphObject = $response->getGraphObject();
        $graphObject = $graphObject->getPropertyAsArray('data');

        foreach ($graphObject as $photo) {
            //echo $photo->getProperty('name');
            $id = $photo->getProperty('id');
            echo "<img src='" . getCoverPhoto($id, $session) . "' />";
            echo '<br/>';
        }


        // echo <a href="' . $_SESSION['logoutUrlFB'] . '">Logout FB</a>
    } else {
        echo '<a href="loginFB.php">Login with Facebook';
    }
} else {
    echo '<a href="loginFB.php">Login with Facebook</a>';
}

echo "album id: ".htmlspecialchars($_GET["albumId"]);
?>
</body>
</html>