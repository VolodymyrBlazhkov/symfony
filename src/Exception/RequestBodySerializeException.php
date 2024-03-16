<?php

namespace App\Exception;


class RequestBodySerializeException extends \RuntimeException
{
     public function __construct(\Throwable $throwable) {
        parent::__construct('error serializing body', 0, $throwable);
    }
}