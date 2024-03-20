<?php

namespace App\Exception;

class UploadFileException extends \RuntimeException
{
    public function __construct() {
        parent::__construct('Bad file');
    }
}