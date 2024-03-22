<?php

namespace App\Exception;

class BookCcontentNotFoundException extends \RuntimeException
{
     public function __construct() {
        parent::__construct('book content not fund');
    }
}