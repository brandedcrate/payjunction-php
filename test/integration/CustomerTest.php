<?php
require_once('test/bootstrap.php');
class CustomerIntegrationTest extends PHPUnit_Framework_TestCase
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
            'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101'
        );

        parent::setUp();
        $this->client = new CustomerClient($options);

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
        $this->assertTrue(is_integer($this->customer->customerId), "Got a " . gettype($this->customer->customerId) . " instead of an integer. A customer was not created");
    }

    /**
     * @description read a customer
     */
    public function testReadCustomer()
    {
        $customer = $this->client->read($this->customer->customerId);
        $this->assertTrue(is_integer($customer->customerId), "Got a " . gettype($customer->customerId) . " instead of an integer. Customer was not read");
    }

    /**
     * @description update a customer
     */
    public function testUpdateCustomer()
    {
        $response = $this->client->update($this->customer->customerId,$this->createData);
        foreach($this->createData as $key => $value)
        {
            $this->assertEquals($value,$response->{$key},$key . " In response is equal to " . $response->{$key} . ". It should be equal to " . $this->createData[$key]);
        }
    }

    /**
     * @description delete a customer
     */
    public function testDeleteCustomer()
    {
        $response = $this->client->delete($this->customer->customerId);
        //@todo assert that the response status code is 204
        $this->assertFalse($response, "Response contains content, Customer was not deleted");
        $this->assertNull($this->client->read($this->customer->customerId),"The customer was able to be read, It was not deleted");
    }



}
