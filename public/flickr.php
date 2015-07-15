<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
<a href="../index.php">Home</a>
<?php
//session_start();

include ('Flickr/Flickr-dbz.php');
use \DPZ\Flickr;

$flickrApiKey = 'd925ecaf7b9a61d1e2c8344951b2151a';
$flickrApiSecret = '';

// Build the URL for the current page and use it for our callback
$callback = 'http://localhost/sources/public/flickr.php';
$flickr = new Flickr($flickrApiKey, $flickrApiSecret, $callback);
if (!$flickr->authenticate('read'))
{
    die("Hmm, something went wrong...\n");
}
$userNsid = $flickr->getOauthData(Flickr::USER_NSID);
$userName = $flickr->getOauthData(Flickr::USER_NAME);
$userFullName = $flickr->getOauthData(Flickr::USER_FULL_NAME);
$parameters =  array(
    'per_page' => 100,
    'extras' => 'url_sq,path_alias',
);
$response = $flickr->call('flickr.stats.getPopularPhotos', $parameters);
$ok = @$response['stat'];
if ($ok == 'ok')
{
    $photos = $response['photos'];
}
else
{
    $err = @$response['err'];
    die("Error: " . @$err['msg']);
}


?>
</body>
</html>