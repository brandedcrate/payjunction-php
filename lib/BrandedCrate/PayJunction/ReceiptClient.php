<?php
namespace BrandedCrate\PayJunction;

class ReceiptClient extends Client
{
    /**
     * @description Gets the most recent receipt document URIs
     * @param $transactionId
     * @param null $params
     * @return array|mixed
     */
    public function read($transactionId, $params = null)
    {
        return $this->get('/transactions/'.$transactionId.'/receipts/latest', $params);
    }

    /**
     * @description Get a signed thermal receipt HTML document for the transaction.
     * @param $transactionId
     * @param null $params
     * @return array|mixed
     */
    public function readThermal($transactionId, $params = null)
    {
        return $this->get('/transactions/'.$transactionId.'/receipts/latest/thermal', $params);
    }

    /**
     * @description Get a signed full page receipt HTML document.
     * @param $transactionId
     * @param null $params
     * @return array|mixed
     */
    public function readFullPage($transactionId, $params = null)
    {
        return $this->get('/transactions/'.$transactionId.'/receipts/latest/fullpage', $params);
    }

    /**
     * @description Email a receipt. On success, returns HTTP Response
     * “204 No Content” requires to and replyTo as parameters
     * @param $transactionId
     * @param null $params
     * @return array|mixed
     */
    public function email($transactionId, $params = null)
    {
        return $this->post('/transactions/'.$transactionId.'/receipts/latest/email', $params);
    }
}
