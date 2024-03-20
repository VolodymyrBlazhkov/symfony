<?php

namespace App\Exception;

class CategoryExistWithSlugException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('category exist with slug');
    }
}