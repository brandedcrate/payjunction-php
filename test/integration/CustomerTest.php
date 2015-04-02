<?php

namespace BrandedCrate\PayJunction\Test\Integration;

use BrandedCrate\PayJunction;

class CustomerIntegrationTest extends \PHPUnit_Framework_TestCase
{

    private $createData;

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

        $this->client = (new PayJunction\Client($options))->customer();

        parent::setUp();
    }

    public function createCustomer()
    {
        $this->createData = array(
            'companyName' => 'ACME, inc.',
            'email' => 'customer@acme.com',
            'identifier' => 'your-custom-id',
            'firstName' => 'Joe',
            'jobTitle' => 'Wage Slave',
            'lastName' => 'Schmoe',
            'middleName' => 'Ignatius',
            'phone' => '5555551212',
            'phone2' => '1234567890',
            'website' => 'acme.com'
        );

        $this->customer = $this->client->create($this->createData);
    }

    public function testBadCreateCustomer()
    {
        $errors = array();

        try {
            $this->customer = $this->client->create(array());
        } catch (PayJunction\Exception $e) {
            $errors = $e->getResponse()->errors;
        }

        $this->assertEquals(count($errors), 1, 'No errors');
    }

    /**
     * @description create a new customer
     */
    public function testCreateCustomer()
    {
        $this->createCustomer();

        $type = gettype($this->customer->customerId);
        $this->assertTrue(
            is_integer($this->customer->customerId),
            "Got a $type instead of an integer. A customer was not created"
        );
    }

    /**
     * @description read a customer
     */
    public function testReadCustomer()
    {
        $this->createCustomer();

        $type = gettype($this->customer->customerId);
        $customer = $this->client->read($this->customer->customerId);
        $this->assertTrue(
            is_integer($customer->customerId),
            "Got a $type instead of an integer. Customer was not read"
        );
    }

    public function testBadReadCustomer()
    {
        $responseCode;
        $errors;

        try {
            $customer = $this->client->read('98adsf98ndsf98basdfb7sad987');
        } catch (PayJunction\Exception $e) {
            $responseCode = $e->getCode();
            $errors = $e->getResponse()->errors;
        }

        $this->assertEquals($responseCode, 404, 'Response code is not 404');
        $this->assertEquals(count($errors), 1, 'Error count incorrect');
    }

    /**
     * @description update a customer
     */
    public function testUpdateCustomer()
    {
        $this->createCustomer();

        $response = $this->client->update($this->customer->customerId, $this->createData);
        foreach ($this->createData as $key => $value) {
            $message = "$key In response is equal to " .
                $response->{$key} . ". It should be equal to " .
                $this->createData[$key];

            $this->assertEquals($value, $response->{$key}, $message);
        }
    }

    /**
     * @description delete a customer
     */
    public function testDeleteCustomer()
    {
        $this->createCustomer();

        $response = $this->client->delete($this->customer->customerId);
        $this->assertTrue($response, "Unable to delete customer");

        $errors;
        $status;

        try {
            $response = $this->client->read($this->customer->customerId);
        } catch (PayJunction\Exception $e) {
            $status = $e->getCode();
            $errors = $e->getResponse()->errors;
        }

        $this->assertGreaterThan(0, count($errors), 'No errors');
        $this->assertEquals(404, $status, 'Wrong status code');
    }
}
