<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
<a href="../index.php">Home</a>
<?php
require '../vendor/autoload.php';

session_start();

//set_include_path(get_include_path() . PATH_SEPARATOR . '/Drive/src');


$CLIENT_ID = '992670963982-ihu7irtmj6o11br1rvp8lvs7i9peb1df.apps.googleusercontent.com';
$CLIENT_SECRET = '';
$REDIRECT_URI = 'http://localhost/sources/public/drive.php';
$SCOPES = array(
    'https://www.googleapis.com/auth/drive.file',
    'email',
    'profile');


/**
 * Exception thrown when an error occurred while retrieving credentials.
 */
class GetCredentialsException extends Exception {
    protected $authorizationUrl;

    /**
     * Construct a GetCredentialsException.
     *
     * @param authorizationUrl The authorization URL to redirect the user to.
     */
    public function __construct($authorizationUrl) {
        $this->authorizationUrl = $authorizationUrl;
    }

    /**
     * @return the authorizationUrl.
     */
    public function getAuthorizationUrl() {
        return $this->authorizationUrl;
    }

    /**
     * Set the authorization URL.
     */
    public function setAuthorizationurl($authorizationUrl) {
        $this->authorizationUrl = $authorizationUrl;
    }
}

/**
 * Exception thrown when no refresh token has been found.
 */
class NoRefreshTokenException extends GetCredentialsException {}

/**
 * Exception thrown when a code exchange has failed.
 */
class CodeExchangeException extends GetCredentialsException {}

/**
 * Exception thrown when no user ID could be retrieved.
 */
class NoUserIdException extends Exception {}

/**
 * Retrieved stored credentials for the provided user ID.
 *
 * @param String $userId User's ID.
 * @return String Json representation of the OAuth 2.0 credentials.
 */
function getStoredCredentials($userId) {
    return $_COOKIE["credentials"];
    // TODO: Implement this function to work with your database.
    //throw new RuntimeException('Not implemented!');
}

/**
 * Store OAuth 2.0 credentials in the application's database.
 *
 * @param String $userId User's ID.
 * @param String $credentials Json representation of the OAuth 2.0 credentials to
store.
 */
function storeCredentials($userId, $credentials, $userInfo) {
    $_SESSION["userInfo"] = $userInfo;
    setcookie("userId", $userId, time() + (86400 * 30), "/");
    setcookie("credentials", $credentials, time() + (86400 * 30), "/");
    // TODO: Implement this function to work with your database.
    //throw new RuntimeException('Not implemented!');
}

/**
 * Exchange an authorization code for OAuth 2.0 credentials.
 *
 * @param String $authorizationCode Authorization code to exchange for OAuth 2.0
 *                                  credentials.
 * @return String Json representation of the OAuth 2.0 credentials.
 * @throws CodeExchangeException An error occurred.
 */
function exchangeCode($authorizationCode) {
    echo "jsem tu";
    try {
        global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI;
        $client = new Google_Client();

        $client->setClientId($CLIENT_ID);
        $client->setClientSecret($CLIENT_SECRET);
        $client->setRedirectUri($REDIRECT_URI);
        $_GET['code'] = $authorizationCode;
        return $client->authenticate($_GET['code']);
    } catch (Google_Auth_Exception $e) {
        print 'An error occurred: ' . $e->getMessage();
        throw new CodeExchangeException(null);
    }
}

/**
 * Send a request to the UserInfo API to retrieve the user's information.
 *
 * @param String credentials OAuth 2.0 credentials to authorize the request.
 * @return Userinfo User's information.
 * @throws NoUserIdException An error occurred.
 */
function getUserInfo($credentials) {
    $apiClient = new Google_Client();
    $apiClient->setAccessToken($credentials);
    $userInfoService = new Google_Service_Oauth2($apiClient);
    try {
        $userInfo = $userInfoService->userinfo->get();

        if ($userInfo != null && $userInfo->getId() != null) {
            return $userInfo;
        } else {
            echo "No user ID";
        }
    } catch (Exception $e) {
        print 'An error occurred: ' . $e->getMessage();
    }
}

/**
 * Retrieve the authorization URL.
 *
 * @param String $emailAddress User's e-mail address.
 * @param String $state State for the authorization URL.
 * @return String Authorization URL to redirect the user to.
 */
function getAuthorizationUrl($emailAddress, $state) {
    global $CLIENT_ID, $REDIRECT_URI, $SCOPES;
    $client = new Google_Client();

    $client->setClientId($CLIENT_ID);
    $client->setRedirectUri($REDIRECT_URI);
    $client->setAccessType('online');
    $client->setApprovalPrompt('force');
    $client->setState($state);
    $client->setScopes($SCOPES);
    $tmpUrl = parse_url($client->createAuthUrl());
    $query = explode('&', $tmpUrl['query']);
    $query[] = 'user_id=' . urlencode($emailAddress);
    if (isset($tmpUrl['port'])){
        return
            $tmpUrl['scheme'] . '://' . $tmpUrl['host'] . $tmpUrl['port'] .
            $tmpUrl['path'] . '?' . implode('&', $query);
    }else {
        return
            $tmpUrl['scheme'] . '://' . $tmpUrl['host'] .
            $tmpUrl['path'] . '?' . implode('&', $query);
    }
}

/**
 * Retrieve credentials using the provided authorization code.
 *
 * This function exchanges the authorization code for an access token and
 * queries the UserInfo API to retrieve the user's e-mail address. If a
 * refresh token has been retrieved along with an access token, it is stored
 * in the application database using the user's e-mail address as key. If no
 * refresh token has been retrieved, the function checks in the application
 * database for one and returns it if found or throws a NoRefreshTokenException
 * with the authorization URL to redirect the user to.
 *
 * @param String authorizationCode Authorization code to use to retrieve an access
 *                                 token.
 * @param String state State to set to the authorization URL in case of error.
 * @return String Json representation of the OAuth 2.0 credentials.
 * @throws NoRefreshTokenException No refresh token could be retrieved from
 *         the available sources.
 */
function getCredentials($authorizationCode, $state) {
    echo "jsem tu";
    $emailAddress = '';
    try {
        $credentials = exchangeCode($authorizationCode);
        $userInfo = getUserInfo($credentials);
        $emailAddress = $userInfo->getEmail();
        $userId = $userInfo->getId();
        $credentialsArray = json_decode($credentials, true);
        if (isset($credentialsArray['refresh_token'])) {
            storeCredentials($userId, $credentials, $userInfo);
            return $credentials;
        } else {
            $credentials = getStoredCredentials($userId);
            $credentialsArray = json_decode($credentials, true);
            if ($credentials != null &&
                isset($credentialsArray['refresh_token'])) {
                return $credentials;
            }
        }
    } catch (CodeExchangeException $e) {
        print 'An error occurred during code exchange.';
        // Drive apps should try to retrieve the user and credentials for the current
        // session.
        // If none is available, redirect the user to the authorization URL.
        $e->setAuthorizationUrl(getAuthorizationUrl($emailAddress, $state));
        throw $e;
    } catch (NoUserIdException $e) {
        print 'No e-mail address could be retrieved.';
    }
    // No refresh token has been retrieved.
    $authorizationUrl = getAuthorizationUrl($emailAddress, $state);
    throw new NoRefreshTokenException($authorizationUrl);
}

$authUrl = getAuthorizationUrl("", "");

if(isset($_GET['code'])){
    global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI;
    $client = new Google_Client();
    $client->setClientId($CLIENT_ID);
    $client->setClientSecret($CLIENT_SECRET);
    $client->setRedirectUri($REDIRECT_URI);
    $client->setScopes(array(
        'https://www.googleapis.com/auth/drive.file',
        'email',
        'profile'));

    $authUrl = $client->createAuthUrl();
    getCredentials($_GET['code'], $authUrl);

    $userName = $_SESSION["userInfo"]["name"];
    $userEmail = $_SESSION["userInfo"]["email"];
    $_SESSION["necum"] = true;
    $client->getAccessToken();
}

$client2 = new Google_Client();
$client2->setClientId($CLIENT_ID);
$client2->setClientSecret($CLIENT_SECRET);
$client2->setRedirectUri($REDIRECT_URI);
$client2->setScopes(array(
    'https://www.googleapis.com/auth/drive.file',
    'email',
    'profile'));
$client2->getAccessToken();
//echo var_dump($client2);

if(isset($_COOKIE["credentials"])){
    if ($client2->isAccessTokenExpired()) {
        echo "ano expiroval";
        $client2->refreshToken($client2->getRefreshToken());
    }
    var_dump($_COOKIE["credentials"]);
    $service = new Google_Service_Drive($client2);

    // Print the names and IDs for up to 10 files.
    $optParams = array(
        'maxResults' => 10,
    );
    //$results = $service->files->listFiles($optParams);

}



?>
<a href=<?php echo "'" . $authUrl . "'" ?>>Authorize</a>
</body>
</html>