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
class QBRequest
{
    /**
     * Sandbox Base Request URL
     *
     * @var string
     **/
    protected $sandboxBaseUrl = 'https://sandbox-quickbooks.api.intuit.com/v3/';

    /**
     * Production Base Request URL
     *
     * @var string
     **/
    protected $productionBaseUrl = 'https://quickbooks.api.intuit.com/v3/';

    /**
     * Base URL, set based on context
     *
     * @var string
     **/
    protected $baseUrl;

    /**
     * OAuth authentication object
     *
     * @var AppSol\GFQuickbooksOnline\Includes\QBOAuth
     **/
    protected $qbOAuth;

    /**
     * Request client
     *
     * @var Guzzle\Http\Client
     **/
    protected $client;

    /**
     * Quickbooks Realm ID / Customer ID
     *
     * @var string
     **/
    protected $realmId = false;

    /**
     * API Response object
     *
     * @var Guzzle\Http\Message\Response
     **/
    protected $response;

    /**
     * Array of errors encountered while handling the response
     *
     * @var array
     **/
    public $responseErrors = [];

    /**
     * Array of erros encountered while parsing the response
     *
     * @var array
     **/
    public $validationErrors = [];

    /**
     * Cached responses to save re-fetching
     *
     * @var array
     **/
    private $cache = [];

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
            self::$_instance = new QBRequest();
        }

        return self::$_instance;
    }

    /**
     * Class constructor
     * 
     * @return void
     **/
    public function __construct()
    {
    }

    /**
     * Setup function
     * Pass a string to indicate whether to query the sandbox or production endpoints
     *
     * @param string $apistatus
     * @param string $realmId
     * @param AppSol\GFQuickbooksOnline\Includes\QBOAuth $qbOAuth
     * @return void
     **/
    public function setup($apistatus, $realmId, $qbOAuth)
    {
        $this->realmId = $realmId;
        $this->baseUrl = $apistatus == 'production'? $this->productionBaseUrl : $this->sandboxBaseUrl;

        $this->client = new Client($this->baseUrl);
        $oauth = new OauthPlugin(array(
            'consumer_key'    => $qbOAuth->oauthConsumerKey,
            'consumer_secret' => $qbOAuth->oauthConsumerSecret,
            'token'           => $qbOAuth->accessToken,
            'token_secret'    => $qbOAuth->accessTokenSecret
        ));
        $this->client->addSubscriber($oauth);
    }

    /**
     * Setter for oauthConsumerSecret
     *
     * @return void
     **/
    public function setRealmId($realmId)
    {
        $this->realmId = $realmId;
    }

    /**
     * Call the GET REST endpoint
     *
     * @return mixed
     * @author 
     **/
    private function getResponse($requestUrl)
    {
        $cacheKey = md5($requestUrl);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        try {
            $this->response = $this->client
                            ->get($requestUrl)
                            ->setHeader('Accept', 'application/json')
                            ->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $e) {
            $this->responseErrorHandler($e->getRequest(), $e->getResponse(), $e->getMessage());
            return false;
        }

        if ($responseBody = $this->validateResponseBody()) {
            $this->cache[$cacheKey] = $responseBody;
        }
        return $responseBody;
    }

    /**
     * Return the response body as a PHP array
     *
     * @return array
     **/
    protected function getResponseBody()
    {
        try {
            return $this->response->json();
        } catch (\Exception $e) {
            $this->responseErrors['json'] = $e->getMessage();
            return [];
        }
    }

    /**
     * Validates the response body for errors and stores the errors
     *
     * @return void
     **/
    protected function validateResponseBody()
    {
        $response = $this->getResponseBody();
        if (isset($response['Fault'])) {
            foreach ($response['Fault']['Error'] as $error) {
                $this->validationErrors[] = [
                    'type' => $response['Fault']['type'],
                    'code' => $error['code'],
                    'element' => isset($error['element'])? $error['element'] : 'general',
                    'message' => $error['Message'],
                    'detail' => $error['Detail']
                ];
            }
            return false;
        }
        return $response;
    }

    /**
     * Call the read REST endpoint
     *
     * @return void
     **/
    public function read($entity, $id)
    {
        $requestUrl = $this->baseUrl . 'company/' . $this->realmId . '/';
        $requestUrl.= $entity . '/' . $id;
        return $this->getResponse($requestUrl);
    }

    /**
     * Call the POST create REST endpoint
     *
     * @return void
     **/
    public function create($endpoint, $data)
    {

    }

    /**
     * Call the POST update REST endpoint
     *
     * @return void
     **/
    public function update($endpoint, $data)
    {
        
    }

    /**
     * Call the GET query REST endpoint
     *
     * @return void
     **/
    public function query($query)
    {
        $requestUrl = $this->baseUrl . 'company/' . $this->realmId;
        $requestUrl.= '/query?query=' . rawurlencode($query);
        return $this->getResponse($requestUrl);
    }

    /**
     * Handle HTTP response code errors
     *
     * @return void
     **/
    private function responseErrorHandler($request, $response, $message = '')
    {
        $error = [
            'url' => $request->getUrl(),
            'message' => $message,
            // 'request' => $request,
            'status' => $response->getStatusCode(),
            // 'response' => $response
        ];
        error_log(print_r($error, true));
        $this->responseErrors[] = $error;
    }

    /**
     * Get the details of the company we are connected to
     *
     * @return array
     **/
    public function getCompanyInfo()
    {
        return $this->read('companyinfo', $this->realmId);
    }

} // END class 