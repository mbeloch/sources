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
    $request = $fb->request('GET', '/me?fields=albums{cover_photo,name}');
    $response = fbRequest($fb, $request);

    $graphNode = $response->getGraphNode();
    $graphNode2 = $graphNode ->asArray();

    for ($i=0; $i<count($graphNode2['albums']); $i++){
        if(isset($graphNode2['albums'][$i]['cover_photo'])){
            $photos = getCoverPhoto2($fb, $graphNode2['albums'][$i]["cover_photo"]["id"]);
            $graphNode2['albums'][$i]["image"] = $photos['images'][count($photos['images'])-2]['source'];
        }
    }
    /*
    foreach ($graphNode2['albums'] as $album){
        if(isset($album['cover_photo'])){
            $photos = getCoverPhoto2($fb, $album["cover_photo"]["id"]);
            //var_dump($photos);
            $album["image"] = $photos['images'][count($photos['images'])-2]['source'];
        }
    }
    */

    header('Content-Type: application/json');
    echo json_encode($graphNode2);
}