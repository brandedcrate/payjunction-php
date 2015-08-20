<?php
namespace BrandedCrate\PayJunction;

use RuntimeException;

class Exception extends RuntimeException
{
    const MESSAGE = 'There was a problem with the request';

    public $response;

    public function __construct($response, $code)
    {
        $this->response = $response;

        parent::__construct(self::MESSAGE, $code);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
