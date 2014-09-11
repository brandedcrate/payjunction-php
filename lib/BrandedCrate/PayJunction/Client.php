<?php namespace BrandedCrate\PayJunction;

use BrandedCrate\PayJunction\TransactionClient;
use BrandedCrate\PayJunction\CustomerClient;
use BrandedCrate\PayJunction\ReceiptClient;

class Client
{

    public $liveEndpoint = 'https://api.payjunction.com';
    public $testEndpoint = 'https://api.payjunctionlabs.com';
    public $packageVersion = '0.0.1';
    public $userAgent;

    public function __construct($options)
    {
        $this->options = $options;

        $this->userAgent = 'PayJunctionPHPClient/' .
            $this->packageVersion .
            '(BrandedCrate; ' .
            phpversion() .
            ')';

        $this->setEndpoint($options['endpoint']);
    }

    public function setEndpoint($endpoint)
    {
        if ($endpoint == 'test') {
            $this->endpoint = $this->testEndpoint;
        } elseif ($endpoint == 'live') {
            $this->endpoint = $this->liveEndpoint;
        } elseif (is_string($endpoint)) {
            $this->endpoint = $endpoint;
        }
    }

    /**
     * @description initializes the curl handle with default configuration and settings
     * @param null $handle
     * @return $this
     */
    public function initCurl($handle = null)
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->options['username'] . ":" . $this->options['password']);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
          "X-PJ-Application-Key: {$this->options['appkey']}",
          "User-Agent: $this->userAgent",
        ));

        return $this;
    }


    /**
     * @description takes the response from our curl request and turns it into an object if necessary
     * @param $response
     * @param null $contentType
     * @return array|mixed
     */
    public function processResponse($response)
    {
        $contentType   = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);
        $responseCode  = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $contentLength = curl_getinfo($this->curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        if ($contentType == 'application/json') {
            $response = json_decode($response);
        }

        if ($responseCode < 200 && $responseCode >= 300) {
            throw new Exception($response, $responseCode);
        }

        if ($contentLength == 0) {
            return true;
        }

        return $response;
    }


    /**
     * @description processes a curl post request
     * @param $path
     * @param null $params
     * @return array|mixed
     */
    public function post($path, $params = null)
    {
        $this->initCurl();

        curl_setopt($this->curl, CURLOPT_POST, TRUE);
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . $path);

        if (is_object($params) || is_array($params)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        return $this->processResponse(curl_exec($this->curl));
    }

    /**
     * @description processes a curl get request
     * @param $path
     * @param null $params
     * @return array|mixed
     */
    public function get($path, $params = null)
    {
        $this->initCurl();

        //create the query string if there are any parameters that need to be passed
        $query_string = "";
        if (!is_null($params)) {
            $query_string = "?" . http_build_query($params,'','&');
        }

        curl_setopt($this->curl, CURLOPT_HTTPGET, TRUE);
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . $path . $query_string);


        $response = $this->processResponse(curl_exec($this->curl));
        $responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        // for some reason payjunction returns 204 instead of 404
        if ($responseCode === 204) {
            return false;
        }

        return $response;
    }


    /**
     * @description processes a curl put request
     * @param $path
     * @param null $params
     * @return array|mixed
     */
    public function put($path, $params = null)
    {
        $this->initCurl();

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if (is_object($params) || is_array($params)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . $path);

        return $this->processResponse(curl_exec($this->curl));
    }

    /**
     * @description processes a curl delete request
     * @param $path
     * @param null $params
     * @return array|mixed
     */
    public function del($path, $params = null)
    {
        $this->initCurl();

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");

        if (is_object($params) || is_array($params)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint . $path);

        return $this->processResponse(curl_exec($this->curl));
    }


    /**
     * @description returns an instance of the receipt client
     * @return ReceiptClient
     */
    public function receipt()
    {
        if(!isset($this->receiptClient) && isset($this->options))
        {
            $this->receiptClient = new ReceiptClient($this->options);
        }
        return $this->receiptClient;
    }


    /**
     * @description returns an instance of the transaction client
     * @return TransactionClient
     */
    public function transaction()
    {
        if(!isset($this->transactionClient) && isset($this->options))
        {
            $this->transactionClient = new TransactionClient($this->options);
        }
        return $this->transactionClient;

    }

    /**
     * @description returns an instance of the customer client
     * @return CustomerClient
     */
    public function customer()
    {
        if(!isset($this->customerClient) && isset($this->options))
        {
            $this->customerClient = new CustomerClient($this->options);
        }
        return $this->customerClient;
    }


}
