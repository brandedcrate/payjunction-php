<?php

namespace BrandedCrate\PayJunction\Test\Integration;

use BrandedCrate\PayJunction;

class CustomerVaultTest extends \PHPUnit_Framework_TestCase
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

        $this->client = new PayJunction\Client($options);

        parent::setUp();
    }

    public function createCustomer()
    {
        return $this->client->customer()->create(array(
            'email' => 'customer@acme.com',
            'firstName' => 'Joe',
            'lastName' => 'Schmoe',
        ));
    }

    public function createCustomerVaultMc($customer)
    {
        return $this->client->customerVault()
            ->create($customer->customerId, array(
                'cardNumber' => '5105105105105100',
                'cardExpMonth' => '9',
                'cardExpYear' => '24',
                'address' => '1600 Pennsylvania Ave NW',
                'city' => 'Washington',
                'state' => 'DC',
                'zip' => 20500
            ));
    }

    public function createCustomerVaultVisa($customer)
    {
        return $this->client->customerVault()
            ->create($customer->customerId, array(
                'cardNumber' => '4242424242424242',
                'cardExpMonth' => '2',
                'cardExpYear' => '25'
            ));
    }

    public function createCustomerVaultAch($customer)
    {
        return $this->client->customerVault()
            ->create($customer->customerId, array(
                'achRoutingNumber' => '104000016',
                'achAccountNumber' => '123456789',
                'achAccountType' => 'CHECKING',
                'achType' => 'CCD'
            ));
    }

    public function testCreateCustomerVault()
    {
        $customer = $this->createCustomer();
        $vault = $this->createCustomerVaultMc($customer);

        $type = gettype($vault->vaultId);
        $this->assertTrue(
            is_integer($vault->vaultId),
            "Got a $type instead of an integer. A vault was not created"
        );
    }

    public function testReadCustomerVault()
    {
        $customer = $this->createCustomer();
        $vaultId = $this->createCustomerVaultMc($customer)->vaultId;

        $vault = $this->client->customerVault()
            ->read($customer->customerId, $vaultId);

        $this->assertEquals($vault->lastFour, 5100, 'Credit card is not in vault');
    }

    public function testIndexCustomerVault()
    {
        $customer = $this->createCustomer();
        $vault1Id = $this->createCustomerVaultMc($customer)->vaultId;
        $vault2Id = $this->createCustomerVaultVisa($customer)->vaultId;

        $vaults = $this->client->customerVault()
            ->index($customer->customerId);

        $vaultIdsInIndex = array(
            $vaults->results[0]->vaultId,
            $vaults->results[1]->vaultId,
        );

        $this->assertEquals(in_array($vault1Id, $vaultIdsInIndex), true, 'Missing vault 1');
        $this->assertEquals(in_array($vault2Id, $vaultIdsInIndex), true, 'Missing vault 2');
    }

    public function testUpdateCustomerVault()
    {
        $customer = $this->createCustomer();
        $vaultId = $this->createCustomerVaultAch($customer)->vaultId;

        $vault = $this->client->customerVault()
            ->update($customer->customerId, $vaultId, array(
                'address' => 'somenewaddress'
            ));

        $this->assertEquals($vault->address->address, 'somenewaddress', 'Address unchanged');
    }

    public function testDeleteCustomerVault()
    {
        $customer = $this->createCustomer();
        $vaultId = $this->createCustomerVaultAch($customer)->vaultId;

        $response = $this->client->customerVault()
            ->delete($customer->customerId, $vaultId);

        $this->assertEquals($response, true, 'Vault not deleted');
    }
}
