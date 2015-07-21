<?php

$fb = new Facebook\Facebook([
    'app_id' => '516525445096951',
    'app_secret' => '',
    'default_graph_version' => 'v2.4',
]);


function getCoverPhoto($fb, $photoId){
    $request = $fb->request('GET', $photoId . '/?fields=images');
    $response = fbRequest($fb, $request);
    $graphNode = $response->getGraphNode();
    $graphNode = $graphNode->asArray();
    $index = count($graphNode['images']);
    return $graphNode['images'][$index-2]['source'];
}

function fbRequest($fb, $request){
    try {
        $response = $fb->getClient()->sendRequest($request);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    return $response;
}

function fbRequest2($fb, $request){
    try {
        $response = $fb->get($request);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    return $response;
}

function userAlbums($fb){
    //$request = $fb->request('GET', '/me?fields=albums{cover_photo,name}');
    $request = '/me?fields=albums{cover_photo,name}';

    $response = fbRequest2($fb, $request);

    $graphNode = $response->getGraphNode();
    $graphNode = $graphNode->asArray();

    foreach($graphNode['albums'] as $neco){
        print_r($neco['name']);
        echo "<br/>";
        if(isset($neco['cover_photo'])){
            echo "<a href='facebook-photos-new.php?albumId=".$neco['id']."'><img src='" . getCoverPhoto($fb, $neco['cover_photo']['id']) . "' /></a>";
            echo "<br/>";
        }else{
            echo "neni nahled";
            echo "<br/>";
        }

    }
}

function userPhotos($fb, $albumId){
    $request = $fb->request('GET', $albumId . '?fields=photos{id,images}');
    //$request = $albumId . '?fields=photos{id,images}';

    $response = fbRequest($fb, $request);
    $phpsmrdi = $response->getGraphEdge();
    //$nextFeed = $fb->next($feedEdge);
    $graphNode = $response->getGraphNode();
    //$feedEdge = $graphNode->getGraphEdge();

    $graphNode = $graphNode->asArray();
    //var_dump($graphNode);

    foreach($graphNode['photos'] as $photo){
        echo "<a href='facebook-download.php?photoId=".$photo['id']."' target='_blank'><img src='" . getCoverPhoto($fb, $photo['id']) . "' /></a>";
    }
}

function downloadUrl($fb, $photoId){
    $request = $fb->request('GET', $photoId . '?fields=images');
    $response = fbRequest($fb, $request);

    $graphNode = $response->getGraphNode();
    $graphNode = $graphNode->asArray();

    return $graphNode['images'][0]['source'];
}