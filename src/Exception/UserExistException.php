<?php

namespace App\Exception;

class UserExistException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('user exist');
    }
}