<?php
require_once('test/bootstrap.php');
use BrandedCrate\PayJunction\ReceiptClient;
use BrandedCrate\PayJunction\TransactionClient;

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
            'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101'
        );

        parent::setUp();
        $this->client = new ReceiptClient($options);
        $this->transactionClient = new TransactionClient($options);

        $this->createData = array(
            'achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );



        $this->transaction = $this->transactionClient->create($this->createData);
    }

    /**
     * @description returns a random number with two decimal places and no commas
     * @return string
     */
    private function getRandomAmountBase()
    {
        return number_format(rand(1,100),2,'.','');
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
        $response = $this->client->email($this->transaction->transactionId,$data);
        //@todo assert that the response status code is 204
        $this->assertNull($response,'Response was not null, email failed');
    }

    /**
     * @description read a receipt
     */
    public function testReadReceipt(){
        $response = $this->client->read($this->transaction->transactionId);
        //@todo assert that the response status code is 200
        $this->assertObjectHasAttribute('documents',$response);
    }

    /**
     * @description read a thermal receipt
     */
    public function testReadThermal(){
        $response = $this->client->readThermal($this->transaction->transactionId);
        //@todo assert that the response content-type is text/html
        $this->assertGreaterThan(0,strlen($response),"thermal response is not greater than 0");
    }

    /**
     * @description read a full page receipt
     */
    public function readFullpage()
    {
        $response = $this->client->readFullPage($this->transaction->transactionId);
        $this->assertGreaterThan(0,strlen($response),"full page receipt response is not greater than 0");
    }



}
