<?php
namespace BrandedCrate\PayJunction;

class CustomerVaultClient extends Client
{
    /**
     * create a new customer vault
     * @param $customerId
     * @param $params
     * @return array|mixed
     */
    public function create($customerId, $params)
    {
        return $this->post("/customers/$customerId/vaults", $params);
    }

    /**
     * read a customer vault
     * @param $customerid
     * @param $id
     * @return array|mixed
     */
    public function read($customerId, $id)
    {
        return $this->get("/customers/$customerId/vaults/$id");
    }

    /**
     * index all customer vaults
     * @param $customerid
     * @return array|mixed
     */
    public function index($customerId)
    {
        return $this->get("/customers/$customerId/vaults");
    }

    /**
     * update existing customer vault
     * @param $customerId
     * @param $id
     * @param null $params
     * @return array|mixed
     */
    public function update($customerId, $id, $params = null)
    {
        return $this->put("/customers/$customerId/vaults/$id", $params);
    }

    /**
     * delete a customer vault
     * @param $customerId
     * @param $id
     * @return array|mixed
     */
    public function delete($customerId, $id)
    {
        return $this->del("/customers/$customerId/vaults/$id");
    }
}
