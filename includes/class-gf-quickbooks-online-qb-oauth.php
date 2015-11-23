<?php
/**
 * Interface class for the Intuit Quickbooks API SDK
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/includes
 */
namespace AppSol\GFQuickbooksOnline\Includes;

use AppSol\GFQuickbooksOnline\Admin as Admin;
use AppSol\GFQuickbooksOnline\Pub as Pub;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

/**
 * Provides access to the functionality of the Intuit Quickbooks API SDK
 *
 * @since      0.1.0
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/includes
 * @author     Stuart Laverick <stuart@appropriatesolutions.co.uk>
 **/
class QBOAuth
{

    /**
     * OAuth Consumer Key
     *
     * @var string
     **/
    public $oauthConsumerKey = false;

    /**
     * OAuth Consumer Secret
     *
     * @var string
     **/
    public $oauthConsumerSecret = false;

    /**
     * Quickbooks App Token
     *
     * @var string
     **/
    public $qbAppToken = false;

    /**
     * OAuth Access Token
     *
     * @var array
     **/
    public $accessToken;

    /**
     * OAuth Access Token Secret
     *
     * @var string
     **/
    public $accessTokenSecret;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    public $dataSource;

    /**
     * OAuth Request Token
     *
     * @var string
     **/
    public $requestToken;

    /**
     * URL for the OAuth provider to return the user to
     *
     * @var string
     **/
    private $callbackUrl = '';

    /**
     * The prefix for all transients stored by this class
     *
     * @var string
     **/
    private $transientPrefix = 'gf-quickbooks-online';

    /**
     * The default time to store transient data for in seconds
     *
     * @var int
     **/
    private $transientLifetime;

    /**
     * Error messages returned from the remote API
     *
     * @var array
     **/
    public $connectionErrors = [];

    /**
     * Static instance of Wheniwork\OAuth1\Client\Server\Intuit
     *
     * @var Wheniwork\OAuth1\Client\Server\Intuit
     **/
    private $oauth = null;

    /**
     * Static class instance
     *
     * @var AppSol\GFQuickbooksOnline\Includes\QBApi
     **/
    private static $_instance = null;

    /**
     * Return the single instance of the class
     *
     * @return AppSol\GFQuickbooksOnline\Includes\QBApi
     **/
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new QBOAuth();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @param $consumerKey
     * @param $consumerSecret
     * @return void
     **/
    public function __construct()
    {
        if (isset($_GET['page'])) {
            $this->callbackUrl = admin_url('admin.php?page=' .
                $_GET['page'] .
                (isset($_GET['subview'])? '&subview=' . $_GET['subview'] : '') .
                '&qbcallback');
        }

        $this->transientLifetime = 60 * 60 * 24 * 30;

        if ($tokenCredentials = $this->getTemporaryData('token-credentials')) {
            $this->accessToken = $tokenCredentials->getIdentifier();
            $this->accessTokenSecret = $tokenCredentials->getSecret();
        }
    }

    /**
     * Setter for oauthConsumerKey
     *
     * @return void
     **/
    public function setConsumerKey($consumerKey)
    {
        $this->oauthConsumerKey = $consumerKey;
    }

    /**
     * Setter for oauthConsumerSecret
     *
     * @return void
     **/
    public function setConsumerSecret($consumerSecret)
    {
        $this->oauthConsumerSecret = $consumerSecret;
    }

    /**
     * Get the OAuth Request Token from Quickbooks
     *
     * @return bool
     **/
    public function getRequestToken()
    {
        if ($this->oauthConsumerKey && $this->oauthConsumerSecret) {
            $this->deleteTemporaryData('token-credentials');
            try {
                $oauth = $this->getOAuth();
                $this->requestToken = $oauth->getTemporaryCredentials();
                $this->storeTemporaryData('request-token', $this->requestToken, 60 * 60);
                return true;
            } catch(\Exception $e) {
                $this->connectionErrors[] = $e->getMessage();
                error_log($this->oauthConsumerKey);
                error_log($this->oauthConsumerSecret);
                error_log($this->callbackUrl);
                return false;
            }
        }
        return false;
    }

    /**
     * Attempts to start the authentication process
     *
     * @return void
     **/
    public function authoriseApp()
    {
        if ($this->getRequestToken()) {
            // wp_redirect($this->oauthAuthoriseUrl . '?oauth_token=' . $this->requestToken['oauth_token']);
            $oauth = $this->getOAuth();
            $oauth->authorize($this->requestToken);
        }
    }

    /**
     * Attempt to exchange the request token for an Access token
     *
     * @return bool
     **/
    public function requestAccessToken()
    {
        if ( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) ){
            try {
                $oauth = $this->getOAuth(true);
                $this->requestToken = $this->getTemporaryData('request-token');
                // request an access token from Intuit
                $tokenCredentials = $oauth->getTokenCredentials($this->requestToken, $_GET['oauth_token'], $_GET['oauth_verifier']);
                error_log(print_r($tokenCredentials, true));
                $this->storeTemporaryData('token-credentials', $tokenCredentials);
                $this->accessToken = $tokenCredentials->getIdentifier();
                $this->accessTokenSecret = $tokenCredentials->getSecret();
                $this->realmId = $_REQUEST['realmId'];  // realmId is legacy for customerId
                $this->storeTemporaryData('realm-id', $this->realmId);
                $this->dataSource = $_REQUEST['dataSource'];
                $this->storeTemporaryData('data-source', $this->dataSource);
                return true;
            } catch(\Exception $e) {
                $this->connectionErrors[] = $e->getMessage();
                error_log(print_r($e, true));
                return false;
            }
        }
        return false;
    }

    /**
     * Manages the process flow during an OAuth authentication round
     *
     * @return void
     **/
    public function processOAuthRequest()
    {
        if (isset($_GET['qbconnect'])) {
            $this->authoriseApp();
        } elseif (isset($_GET['qbcallback'])) {
            $this->requestAccessToken();
        }
    }

    /**
     * Instansiate and return the Intuit OAuth object
     *
     * @return \OAuth
     **/
    private function getOAuth()
    {
        // $oauth = new \OAuth($this->oauthConsumerKey, $this->oauthConsumerSecret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
        if ($this->oauth == null) {
            $this->oauth = new \Wheniwork\OAuth1\Client\Server\Intuit(array(
                'identifier'   => $this->oauthConsumerKey,
                'secret'       => $this->oauthConsumerSecret,
                'callback_uri' => $this->callbackUrl,
            ));
        }
        return $this->oauth;
    }

    /**
     * Stores temporary data for the required time
     *
     * @return bool success
     * @author Stuart Laverick
     **/
    protected function storeTemporaryData($key, $value, $expires = null)
    {
        $expires? : $this->transientLifetime;
        return set_transient(md5($this->transientPrefix . '-' . $key), $value, $expires);
    }

    /**
     * Retrieves stored temporary data
     *
     * @return mixed
     * @author Stuart Laverick
     **/
    protected function getTemporaryData($key)
    {
        return get_transient(md5($this->transientPrefix . '-' . $key));
    }

    /**
     * Delete stored temporary data
     *
     * @return Bool success
     * @author Stuart Laverick
     **/
    protected function deleteTemporaryData($key)
    {
        return delete_transient(md5($this->transientPrefix . '-' . $key));
    }


} // END class 
