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

require_once("Flickr/phpFlickr.php");
$f = new phpFlickr("d925ecaf7b9a61d1e2c8344951b2151a", "b033177611b72fa7");
//change this to the permissions you will need
if (!isset($_SESSION['phpFlickr_auth_token'])){
    $f->auth("read");
    $_SESSION['phpFlickr_auth_token'] = $f->auth_getToken($f->auth_getFrob());
}
$_SESSION['phpFlickr_auth_token'] = $f->auth_getToken($f->auth_getFrob());

print_r($neco);

echo "Copy this token into your code: " . $_SESSION['phpFlickr_auth_token'];


?>
</body>
</html>