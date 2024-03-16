<?php

namespace App\Exception;

class SubscriberExistException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('subscriber exist');
    }
}