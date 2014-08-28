<?php

class PayjunctionClient
{

    public $liveEndpoint = 'https://api.payjunction.com';
    public $testEndpoint = 'https://api.payjunctionlabs.com';
    public $packageVersion = '0.0.1';
    public $userAgent;


    public function __construct()
    {
        $this->userAgent = 'PayJunctionPHPClient/' . $this->packageVersion . '(BrandedCreate; PHP/)'; //@todo add process.version
        $this->baseUrl = $this->testEndpoint; //@todo create a method for setting the base url to either test or live

    }

    public function setEndpoint($endpoint)
    {
        $this->baseUrl = $endpoint;
    }



    /**
     * @description initializes the curl handle with default configuration and settings
     * @param null $handle
     * @return $this
     */
    public function initCurl($handle = null)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false); //Don't worry about validating ssl @todo talk about security concerns
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);


        //if we have a password and username then set it by default to be passed for authentication
        if (isset($this->defaults['password']) && isset($this->defaults['username'])) {
            curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($this->curl, CURLOPT_USERPWD, $this->defaults['username'] . ":" . $this->defaults['password']);
        }

        //if we have default headers to pass then pass them
        if (isset($this->defaults['headers']) && is_array($this->defaults['headers'])) {
            $headers = array();
            foreach ($this->defaults['headers'] as $key => $value) {

                array_push($headers, $key . ': ' . $value);

            }
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        }
        return $this;
    }


    /**
     * @description generates a new client
     * @param null $options
     * @return $this
     */
    public function generateClient($options = null)
    {
        $this->baseUrl = isset($options['endpoint']) ? $options['endpoint'] : $this->baseUrl;
        $this->defaults['username'] = isset($options['username']) ? $options['username'] : '';
        $this->defaults['password'] = isset($options['password']) ? $options['password'] : '';
        $this->defaults['headers']['X-PJ-Application-Key'] = isset($options['appkey']) ? $options['appkey'] : '';
        $this->defaults['headers']['User-Agent'] = $this->userAgent;

        $this->initCurl();


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
        $contentType = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);


        if ($contentType == 'text/html' || is_null($contentType) || !isset($contentType) || $contentType = '' || $contentType == FALSE) {
            return $response;
        }

        try {
            $object = json_decode($response);
            return $object;

        } catch (Exception $e) {
            return array(
                'errors' => array(
                    0 => 'Invalid Response Type, Error In Processing Response From Payjunction'
                )
            );
        }

    }


    /**
     * @description processes a curl post request
     * @param $path
     * @param null $params
     * @return array|mixed
     */
    public function post($path, $params = null)
    {
        curl_setopt($this->curl, CURLOPT_POST, TRUE);
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $path);


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

        //create the query string if there are any parameters that need to be passed
        $query_string = "";
        if (!is_null($params)) {
            $query_string = "?" . http_build_query($params,'','&');
        }

        curl_setopt($this->curl, CURLOPT_HTTPGET, TRUE);
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $path . $query_string);


        return $this->processResponse(curl_exec($this->curl));
    }


    /**
     * @description processes a curl put request
     * @param $path
     * @param null $params
     * @return array|mixed
     */
    public function put($path, $params = null)
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if (is_object($params) || is_array($params)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $path);

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
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");

        if (is_object($params) || is_array($params)) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $path);


        return $this->processResponse(curl_exec($this->curl));
    }


}
