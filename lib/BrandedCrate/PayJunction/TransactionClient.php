<?php
namespace BrandedCrate\PayJunction;

class TransactionClient extends Client
{
    /**
     * @description create a new transaction
     * @param $params
     * @return array|mixed
     */
    public function create($params)
    {
        return $this->post('/transactions', $params);
    }

    /**
     * @description read from an existing transaction
     * @param $id
     * @return array|mixed
     */
    public function read($id)
    {
        return $this->get('/transactions/'.$id);

    }

    /**
     * @description update an existing transaction
     * @param $id
     * @param null $params
     * @return array|mixed
     */
    public function update($id, $params = null)
    {
        return $this->put('/transactions/'.$id, $params);

    }

    /**
     * @description add a signature to an existing transaction
     * @param $id
     * @param $params
     * @return array|mixed
     */
    public function addSignature($id, $params)
    {
        return $this->post('/transactions/'.$id.'/signature/capture', $params);

    }
}
