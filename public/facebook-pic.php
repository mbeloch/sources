<?php
session_start();

require '../vendor/autoload.php';
require 'facebook-functions.php';

if(!isset($_SESSION['facebook_access_token'])){
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['user_photos'];
    $loginUrl = $helper->getLoginUrl('http://localhost/sources/public/fb-login-callback.php', $permissions);

    //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
    $data = array('login' => $loginUrl);
    header('Content-Type: application/json');
    echo json_encode($data);

}else {
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    if(isset($_GET["albumId"])){
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        $albumId = htmlspecialchars($_GET["albumId"]);
        $fotky = userPhotos3($fb, $albumId);


        for ($i=0; $i<count($fotky["data"]); $i++){
            $photos = getCoverPhoto2($fb, $fotky["data"][$i]["id"]);
            //$fotky["data"][$i]["image"] = $photos["images"][count($photos["images"])-2]["source"];
            $fotky["data"][$i]["images"] = $photos["images"];

        }

        header('Content-Type: application/json');
        echo json_encode($fotky);
    }

    if(isset($_GET["next"])){
        $url = htmlspecialchars($_GET["next"]);
        $request = $fb->request('GET', $url);
        $response = fbRequest($fb, $request);
        $graphNode = $response->getGraphNode();
        var_dump($response);
        //$graphNode = $graphNode->asArray();
        /*
        $photos = array();
        $photos["data"] = array();
        $photos["paging"] = array();

        foreach ($graphEdge as $graphNode) {
            $photos["data"][] = $graphNode->asArray();
        }
        $nextUrl = $graphEdge->getMetaData();
        if(isset($nextUrl["paging"]["next"])){
            $photos["paging"]["next"] = $nextUrl["paging"]["next"];
        }
        */
        header('Content-Type: application/json');
        echo json_encode($graphNode);
    }
}