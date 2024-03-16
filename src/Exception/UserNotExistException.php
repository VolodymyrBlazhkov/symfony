<?php

namespace App\Exception;

class UserNotExistException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('user not exist');
    }
}