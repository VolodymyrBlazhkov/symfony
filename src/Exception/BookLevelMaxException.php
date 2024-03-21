<?php

namespace App\Exception;

class BookLevelMaxException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('book level va[');
    }
}