<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
	<a href="public/facebook.php">Facebook</a><br/>
    <a href="public/dropbox-login.php">Dropbox</a><br/>
<?php
session_start();

$_SESSION['Drobpox'] = false;
$_SESSION['Drobpox_token'] = null;
?>
</body>
</html>