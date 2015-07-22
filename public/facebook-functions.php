<?php

$fb = new Facebook\Facebook([
    'app_id' => '516525445096951',
    'app_secret' => '',
    'default_graph_version' => 'v2.4',
]);


function getCoverPhoto($fb, $photoId)
{
    $request = $fb->request('GET', $photoId . '/?fields=images');
    $response = fbRequest($fb, $request);
    $graphNode = $response->getGraphNode();
    $graphNode = $graphNode->asArray();
    $index = count($graphNode['images']);
    return $graphNode['images'][$index - 2]['source'];
}

function fbRequest($fb, $request)
{
    try {
        $response = $fb->getClient()->sendRequest($request);
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    return $response;
}

function userAlbums($fb)
{
    if (getFbPermissions($fb)){
        $request = $fb->request('GET', '/me?fields=albums{cover_photo,name}');
        $response = fbRequest($fb, $request);

        $graphNode = $response->getGraphNode();
        $graphNode = $graphNode->asArray();

        foreach ($graphNode['albums'] as $neco) {
            print_r($neco['name']);
            echo "<br/>";
            if (isset($neco['cover_photo'])) {
                echo "<a href='facebook-photos-new.php?albumId=" . $neco['id'] . "'><img src='" . getCoverPhoto($fb, $neco['cover_photo']['id']) . "' /></a>";
                echo "<br/>";
            } else {
                echo "neni nahled";
                echo "<br/>";
            }
        }
        echo "<a href='facebook-photos-new.php?albumId=me'><img src='" . getCoverPhoto($fb, $neco['cover_photo']['id']) . "' /></a>";
    }else {
        echo "nemas permisn potvrd ho";
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['user_photos']; // optional
        $loginUrl = $helper->getReRequestUrl('http://localhost/sources/public/fb-login-callback.php', $permissions);
        echo '<a href="' . $loginUrl . '">ziskej fotky permission!</a>';
    }
}

function fbNext($fb, $nextFeed){
    if ($nextFeed){
        $dalsiFeed = $fb->next($nextFeed);
        foreach ($nextFeed as $graphNode) {
            $smrdi = $graphNode->getField('id');
            echo "<a href='facebook-download.php?photoId=" . $smrdi . "' target='_blank'><img src='" . getCoverPhoto($fb, $smrdi) . "' /></a>";
        }
        if ($dalsiFeed){
            fbNext($fb, $dalsiFeed);
        }
    }else {
        return;
    }
}

function userPhotos($fb, $albumId)
{
    if($albumId == 'me'){
        $albumId = '1540715021';
    }
    $request = $fb->request('GET', $albumId . '/photos');
    //$request = $albumId . '?fields=photos{id,images}';

    $response = fbRequest($fb, $request);
    $graphEdge = $response->getGraphEdge();

    fbNext($fb, $graphEdge);
}

function downloadUrl($fb, $photoId)
{
    $request = $fb->request('GET', $photoId . '?fields=images');
    $response = fbRequest($fb, $request);

    $graphNode = $response->getGraphNode();
    $graphNode = $graphNode->asArray();

    return $graphNode['images'][0]['source'];
}

function getFbPermissions($fb){
    $request = $fb->request('GET', '/me?fields=permissions');
    $response = fbRequest($fb, $request);

    $graphNode = $response->getGraphNode();
    $graphNode = $graphNode->asArray();
    $photoPerm = false;
    foreach($graphNode['permissions'] as $permission){
        if (($permission['permission'] == 'user_photos') && ($permission['status'] == 'granted')){
            $photoPerm = true;
        }
    }

    return $photoPerm;
}