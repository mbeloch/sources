<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
	<a href="public/facebook.php">Facebook</a><br/>
    <a href="public/dropbox-login.php">Dropbox</a><br/>
    <a href="public/flickr.php">Flickr</a><br/>
    <a href="public/drive.php">Google Drive</a><br/>
    <a href="public/drive2.php">Google Drive - test</a><br/>
<?php
session_start();

$_SESSION['google'] = null;
?>
</body>
</html>
