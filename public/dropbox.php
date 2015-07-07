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

if (!(isset($_SESSION['Drobpox']) && $_SESSION['Drobpox'] == true)) {
    function getWebAuth()
    {
        $appInfo = dbx\AppInfo::loadFromJsonFile("Dropbox/config.json");
        $clientIdentifier = "my-app/1.0";
        $redirectUri = "http://localhost/sources/public/dropbox.php";
        $csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
        return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
    }

    try {
        list($accessToken, $userId, $urlState) = getWebAuth()->finish($_GET);
        assert($urlState === null);  // Since we didn't pass anything in start()
    }
    catch (dbx\WebAuthException_BadRequest $ex) {
        error_log("/dropbox-auth-finish: bad request: " . $ex->getMessage());
        // Respond with an HTTP 400 and display error page...
    }
    catch (dbx\WebAuthException_BadState $ex) {
        // Auth session expired.  Restart the auth process.
        header('Location: /dropbox-auth-start');
    }
    catch (dbx\WebAuthException_Csrf $ex) {
        error_log("/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage());
        // Respond with HTTP 403 and display error page...
    }
    catch (dbx\WebAuthException_NotApproved $ex) {
        error_log("/dropbox-auth-finish: not approved: " . $ex->getMessage());
    }
    catch (dbx\WebAuthException_Provider $ex) {
        error_log("/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage());
    }
    catch (dbx\Exception $ex) {
        error_log("/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage());
    }
    $_SESSION['Drobpox'] = true;
// We can now use $accessToken to make API requests.
    $client = new dbx\Client($accessToken, $userId, $urlState);
    $_SESSION['token'] = $client;
    $client->getAccessToken();
}else {
    print_r($_SESSION['token']);
}


?>
</body>
</html>