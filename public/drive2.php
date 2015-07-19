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
$CLIENT_ID = '992670963982-ihu7irtmj6o11br1rvp8lvs7i9peb1df.apps.googleusercontent.com';
$CLIENT_SECRET = '';
$REDIRECT_URI = 'http://localhost/sources/public/drive2.php';
$SCOPES = array(
    'https://www.googleapis.com/auth/drive.readonly',
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
    return $_SESSION['google'];
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
function storeCredentials($userId, $credentials) {
    $_SESSION['google'] = $credentials;
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
    //$apiClient->setUseObjects(true);
    $apiClient->setAccessToken($credentials);
    $userInfoService = new Google_Service_Oauth2($apiClient);
    //$userInfo = null;

    try {
        $userInfo = $userInfoService->userinfo->get();

        if ($userInfo != null && $userInfo->getId() != null) {
            return $userInfo;
        } else {
            throw new NoUserIdException();
        }
    } catch (Google_Exception $e) {
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
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');
    $client->setState($state);
    $client->setScopes($SCOPES);
    $tmpUrl = parse_url($client->createAuthUrl());
    $query = explode('&', $tmpUrl['query']);
    $query[] = 'user_id=' . urlencode($emailAddress);
    if(!isset($tmpUrl['port'])){
        $tmpUrl['port'] = '';
    }
    return
        $tmpUrl['scheme'] . '://' . $tmpUrl['host'] . $tmpUrl['port'] .
        $tmpUrl['path'] . '?' . implode('&', $query);
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
    $emailAddress = '';
    try {
        $credentials = exchangeCode($authorizationCode);
        $userInfo = getUserInfo($credentials);
        $emailAddress = $userInfo->getEmail();
        $userId = $userInfo->getId();
        $credentialsArray = json_decode($credentials, true);
        if (isset($credentialsArray['refresh_token'])) {
            storeCredentials($userId, $credentials);
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

function buildService($credentials) {
    $apiClient = new Google_Client();
    //$apiClient->setUseObjects(true);
    $apiClient->setAccessToken($credentials);
    return new Google_Service_Drive($apiClient);
}

/**
 * Retrieve a list of File resources.
 *
 * @param Google_Service_Drive $service Drive API service instance.
 * @return Array List of Google_Service_Drive_DriveFile resources.
 */
function retrieveAllFiles($service) {
    $result = array();
    $pageToken = NULL;

    do {
        try {
            $parameters = array();
            if ($pageToken) {
                $parameters['pageToken'] = $pageToken;
            }
            $files = $service->files->listFiles($parameters);

            $result = array_merge($result, $files->getItems());
            $pageToken = $files->getNextPageToken();
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
            $pageToken = NULL;
        }
    } while ($pageToken);
    return $result;
}

if(isset($_SESSION['google'])){
    echo "pamapalá";
    $service = buildService($_SESSION['google']);
    $result = retrieveAllFiles($service);
    print_r($result);
}


if(isset($_GET['code'])){
    global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI, $SCOPES;
    $client = new Google_Client();
    $client->setClientId($CLIENT_ID);
    $client->setClientSecret($CLIENT_SECRET);
    $client->setRedirectUri($REDIRECT_URI);
    $client->setScopes($SCOPES);

    print_r($_SESSION['google']);

    $authUrl = $client->createAuthUrl();
    getCredentials($_GET['code'], $authUrl);

    //$userName = $_SESSION["userInfo"]["name"];
    //$userEmail = $_SESSION["userInfo"]["email"];
    //$_SESSION["necum"] = true;
    //$client->getAccessToken();
}

?>
<a href=<?php echo "'" . $authUrl . "'" ?>>Authorize</a>
</body>
</html>