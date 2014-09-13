<?php

namespace BrandedCrate\PayJunction\Test\Integration;

use BrandedCrate\PayJunction;

class CustomerIntegrationTest extends \PHPUnit_Framework_TestCase
{

    private $createData;

    /**
     * Runs once before all tests are starte d     *
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

        $this->client = (new PayJunction\Client($options))->customer();

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


    /**
     * @description create a new customer
     */
    public function testCreateCustomer()
    {
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
        $type = gettype($this->customer->customerId);
        $customer = $this->client->read($this->customer->customerId);
        $this->assertTrue(
            is_integer($customer->customerId),
            "Got a $type instead of an integer. Customer was not read"
        );
    }

    /**
     * @description update a customer
     */
    public function testUpdateCustomer()
    {
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
        $response = $this->client->delete($this->customer->customerId);
        $this->assertTrue($response, "Unable to delete customer");

        $response = $this->client->read($this->customer->customerId);
        $this->assertEquals(
            $response->errors[0]->message,
            '404 Not Found',
            'The customer was able to be read, It was not deleted'
        );
    }
}
