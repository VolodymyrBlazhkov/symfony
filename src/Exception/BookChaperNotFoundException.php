<?php

namespace App\Exception;

class BookChaperNotFoundException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('book chaper not fund');
    }
}