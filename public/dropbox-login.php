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

require_once "Dropbox/autoload.php";
use \Dropbox as dbx;

if (isset($_SESSION['Drobpox']) && $_SESSION['Drobpox'] == true) {
    header('Location: http://localhost/sources/public/dropbox.php');
} else {
    function getWebAuth()
    {
        $appInfo = dbx\AppInfo::loadFromJsonFile("Dropbox/config.json");
        $clientIdentifier = "my-app/1.0";
        $redirectUri = "http://localhost/sources/public/dropbox.php";
        $csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
        return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
    }


    $authorizeUrl = getWebAuth()->start();
    header("Location: $authorizeUrl");

}




?>
</body>
</html>