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


//$client = dbx\Client($accessToken);

//echo dbx\appInfo::getKey();

if (isset($_SESSION['Drobpox']) && $_SESSION['Drobpox'] == true){
    echo "necum";
    print_r($_SESSION['Drobpox_token']);
}

function getMetadata($path)
{
    Path::checkArg("path", $path);

    return $this->_getMetadata($path, array("list" => "false"));
}

?>
</body>
</html>