<?php

use BrandedCrate\PayJunction;

class ReceiptIntegrationTest extends PHPUnit_Framework_TestCase
{

    private $createData;

    /**
     * Runs once before all tests are started     *
     */
    public function setUp()
    {
        $options = array(
            'username' => 'pj-ql-01',
            'password' => 'pj-ql-01p',
            'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101',
            'endpoint' => 'test'
        );

        parent::setUp();

        $this->client = new PayJunction\Client($options);

        $this->createData = array(
            'achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );

        $this->transaction = $this->client->transaction()->create($this->createData);
    }

    /**
     * @description returns a random number with two decimal places and no commas
     * @return string
     */
    private function getRandomAmountBase()
    {
        return number_format(rand(1, 100), 2, '.', '');
    }

    /**
     * @description send a test email
     */
    public function testEmail()
    {
        $data = array(
            'to' => 'stephen+automation@brandedcrate.com',
            'replyTo' => 'foobar@whatever.com',
            'requestSignature' => 'true'
        );
        $response = $this->client->receipt()->email($this->transaction->transactionId, $data);
        $this->assertTrue($response, 'Response was not successful, email failed');
    }

    /**
     * @description read a receipt
     */
    public function testReadReceipt()
    {
        $response = $this->client->receipt()->read($this->transaction->transactionId);
        //@todo assert that the response status code is 200
        $this->assertObjectHasAttribute('documents', $response);
    }

    /**
     * @description read a thermal receipt
     */
    public function testReadThermal()
    {
        $response = $this->client->receipt()->readThermal($this->transaction->transactionId);
        //@todo assert that the response content-type is text/html
        $this->assertGreaterThan(0, strlen($response), "thermal response is not greater than 0");
    }

    /**
     * @description read a full page receipt
     */
    public function readFullpage()
    {
        $response = $this->client->receipt()->readFullPage($this->transaction->transactionId);
        $this->assertGreaterThan(0, strlen($response), "full page receipt response is not greater than 0");
    }
}
