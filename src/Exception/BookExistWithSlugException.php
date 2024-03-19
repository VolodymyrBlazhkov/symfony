<?php

namespace App\Exception;

class BookExistWithSlugException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('book exist with slug');
    }
}