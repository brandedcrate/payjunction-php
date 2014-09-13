<?php

use BrandedCrate\PayJunction;

class TransactionUnitTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $options = array(
            'username' => 'pj-ql-01',
            'password' => 'pj-ql-01p',
            'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101',
            'endpoint' => 'http://localhost:8000'
        );

        parent::setUp();
        $this->client = new PayJunction\Client($options);
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

        $transaction = $this->client->transaction()->create($data);
        $this->assertEquals($data, get_object_vars($transaction->post), 'Passed variables are not correct');
        $this->assertEquals('POST', $transaction->request_method, 'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions', $transaction->path, 'The path is incorrect');
    }


    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testRead()
    {
        $transaction = $this->client->transaction()->read(543);

        $this->assertEquals('GET', $transaction->request_method, 'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions/543', $transaction->path, 'The path is incorrect');

    }

    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testUpdate()
    {
        $data = array(
            'foo' => 'baz'
        );

        $transaction = $this->client->transaction()->Update(654, $data);

        $this->assertEquals($data, get_object_vars($transaction->put), 'Passed variables are not correct');
        $this->assertEquals('PUT', $transaction->request_method, 'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions/654', $transaction->path, 'The path is incorrect');

    }

    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testAddSignature()
    {
        $data = array(
            'foo' => 'baa'
        );

        $transaction = $this->client->transaction()->addSignature(655, $data);
        $this->assertEquals($data, get_object_vars($transaction->post), 'Passed variables are not correct');
        $this->assertEquals('POST', $transaction->request_method, 'The PHP Verb Is Incorrect');
        $this->assertEquals('/transactions/655/signature/capture', $transaction->path, 'The path is incorrect');

    }
}
