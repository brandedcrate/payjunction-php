<?php
namespace BrandedCrate\PayJunction;

class CustomerClient extends Client
{
    /**
     * @description create a new customer
     * @param $params
     * @return array|mixed
     */
    public function create($params)
    {
        return $this->post('/customers', $params);
    }

    /**
     * @description read a customer
     * @param $id
     * @return array|mixed
     */
    public function read($id)
    {
        return $this->get("/customers/$id");
    }

    /**
     * @description update record of an existing customer
     * @param $id
     * @param null $params
     * @return array|mixed
     */
    public function update($id, $params = null)
    {
        return $this->put("/customers/$id", $params);
    }


    /**
     * @description delete record of an existing customer
     * @param $id
     * @return array|mixed
     */
    public function delete($id)
    {
        return $this->del("/customers/$id");
    }
}
