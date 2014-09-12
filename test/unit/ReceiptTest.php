<?php

use BrandedCrate\PayJunction;

class ReceiptUnitTest extends PHPUnit_Framework_TestCase
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
        $this->client = (new PayJunction\Client($options))->receipt();
    }

    /**
     * Ensure that the correct verb and path are used for the read method
     */
    public function testRead()
    {
        $transaction = $this->client->read(1234);
        $this->assertEquals('GET', $transaction->request_method, 'The HTTP Verb Is Incorrect');
        $this->assertEquals('/transactions/1234/receipts/latest', $transaction->path, 'The path is incorrect');
    }

    /**
     * Ensure that the correct verb and path are used for the readThermal method
     */
    public function testReadThermal()
    {

        $transaction = $this->client->readThermal(1234);
        $this->assertEquals('GET', $transaction->request_method, 'The HTTP Verb Is Incorrect');
        $this->assertEquals('/transactions/1234/receipts/latest/thermal', $transaction->path, 'The path is incorrect');

    }


    /**
     * Ensure that the correct verb and path are used for the readFullPage method
     */
    public function testReadFullPage()
    {
        $transaction = $this->client->readFullPage(1234);
        $this->assertEquals('GET', $transaction->request_method, 'The HTTP Verb Is Incorrect');
        $this->assertEquals('/transactions/1234/receipts/latest/fullpage', $transaction->path, 'The path is incorrect');
    }

    /**
     * Ensure that the correct verb and path are used for the readFullPage method
     */
    public function testEmail()
    {
        $data = array(
            'hi' => 'hello'
        );
        $transaction = $this->client->email(1234, $data);
        $this->assertEquals('POST', $transaction->request_method, 'The HTTP Verb Is Incorrect');
        $this->assertEquals($data, get_object_vars($transaction->post), 'Passed variables are not correct');
        $this->assertEquals('/transactions/1234/receipts/latest/email', $transaction->path, 'The path is incorrect');
    }
}
