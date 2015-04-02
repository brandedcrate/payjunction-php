<?php

namespace BrandedCrate\PayJunction\Test\Integration;

use BrandedCrate\PayJunction;

class TransactionIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Runs once before all tests are started
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
        $this->client = (new PayJunction\Client($options))->transaction();
    }

    /**
     * Used to test whether a transaction is successful.
     * asserts that the provided transaction is free of errors,
     * the approved value is true and status is capture
     */
    private function isSuccessfulTransaction($transaction, $type = null)
    {
        $this->assertObjectNotHasAttribute(
            'errors',
            $transaction,
            "$type Transaction was not successful, It contained errors."
        );
        $this->assertTrue($transaction->response->approved, $type . " Transaction was not approved");
        $this->assertEquals($transaction->status, "CAPTURE", $type . " Transaction was not a capture");
    }

    /**
     * @description returns a random number with two decimal places and no commas
     * @return string
     */
    private function getRandomAmountBase()
    {
        return number_format(rand(1, 100), 2, '.', '');
    }

    public function testBadTransaction()
    {
        $errors;
        $status;

        try {
            $transaction = $this->client->create(array());
        } catch (PayJunction\Exception $e) {
            $status = $e->getCode();
            $errors = $e->getResponse()->errors;
        }

        $this->assertGreaterThan(0, count($errors), 'Not enough errors');
        $this->assertEquals(400, $status, 'Wrong status code');
    }

    /**
     * @description create an ach transaction
     */
    public function testACHTransaction()
    {
        $data = array(
            'achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );

        $transaction = $this->client->create($data);

        $this->isSuccessfulTransaction($transaction, 'ACH');
    }

    /**
     * @description create a credit card transaction
     */
    public function testCardTransactionCreate()
    {
        $data = array(
            'cardNumber' => '4444333322221111',
            'cardExpMonth' => '01',
            'cardExpYear' => '18',
            'cardCvv' => '999',
            'amountBase' => $this->getRandomAmountBase()
        );

        $transaction = $this->client->create($data);
        $this->isSuccessfulTransaction($transaction, 'Card');
    }

    /**
     * @description create a keyed credit card transaction
     */
    public function testKeyedCardTransactionCreate()
    {
        $data = array(
            'cardTrack' => '%B4444333322221111^First/Last^1712980100000?;4444333322221111=1712980100000?',
            'amountBase' => $this->getRandomAmountBase()
        );
        $this->isSuccessfulTransaction($this->client->create($data), 'Keyed');
    }

    /**
     * @description create and void a transaction
     */
    public function testVoidTransaction()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );
        $transaction = $this->client->create($data);

        $update_data = array(
            'status' => 'VOID'
        );

        $transaction = $this->client->update($transaction->transactionId, $update_data);
        $this->assertEquals('VOID', $transaction->status, "Transaction was not voided");
        $this->assertObjectNotHasAttribute('errors', $transaction, " Transaction has errors");
        $this->assertTrue($transaction->response->approved, " Transaction did not maintain an approved status");
    }

    /**
     * @description create a transaction and add a signature to it
     */
    public function testAddSignature()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );
        $transaction = $this->client->create($data);

        $signature_data = array(
            'signature' => file_get_contents('./test/fixtures/signature-1')
        );
        $transaction = $this->client->addSignature($transaction->transactionId, $signature_data);

        $this->assertEquals('SIGNED', $transaction->signatureStatus, 'The transaction does not have a signed status');
    }

    /**
     * @description create a transaction and then read it
     */
    public function testReadTransaction()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );
        $transaction = $this->client->create($data);
        $read_transaction = $this->client->read($transaction->transactionId);

        $this->assertEquals(
            $transaction->transactionId,
            $read_transaction->transactionId,
            'The created transaction Id is not the same as the read transaction Id'
        );
    }
}
