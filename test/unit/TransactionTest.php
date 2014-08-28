<?php

class TransactionUnitTest extends PHPUnit_Framework_TestCase{

    static $endpoint = 'http://localhost/payjunctionphp/test/echo';

    public function setUp()
    {
        $options = array(
            'username' => 'pj-ql-01',
            'password' => 'pj-ql-01p',
            'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101'
        );

        parent::setUp();
        $this->client = new TransactionClient($options);
        $this->client->setEndpoint(self::$endpoint);

    }

    private function getRequestPath($client = null)
    {
        if(!isset($client)) $client = $this->client;
        $request_path = str_replace($client->baseUrl,'',curl_getinfo($client->curl));
        return $request_path['url'];
    }
    /**
     * Ensure that the correct verb and path are used for the create method
     */
    public function testCreate()
    {

        $data = array(
            'achRoutingNumber' => '987654321',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'foo' => 'bar'
        );


        $transaction = $this->client->create($data);
        $this->assertEquals($data, get_object_vars($transaction->post),'Passed variables are not correct');
        $this->assertEquals('POST', $transaction->request_method,'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions', $this->getRequestPath(), 'The path is incorrect');

    }


    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testRead()
    {
        $transaction = $this->client->read(543);

        $this->assertEquals('GET', $transaction->request_method,'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions/543', $this->getRequestPath(), 'The path is incorrect');

    }

    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testUpdate()
    {
        $data = array(
            'foo' => 'baz'
        );

        $transaction = $this->client->Update(654,$data);

        $this->assertEquals($data, get_object_vars($transaction->put),'Passed variables are not correct');
        $this->assertEquals('PUT', $transaction->request_method,'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions/654', $this->getRequestPath(), 'The path is incorrect');

    }

    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testAddSignature()
    {
        $data = array(
            'foo' => 'baa'
        );

        $transaction = $this->client->addSignature(655,$data);
        $this->assertEquals($data, get_object_vars($transaction->post),'Passed variables are not correct');
        $this->assertEquals('POST', $transaction->request_method,'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions/655/signature/capture', $this->getRequestPath(), 'The path is incorrect');

    }



}