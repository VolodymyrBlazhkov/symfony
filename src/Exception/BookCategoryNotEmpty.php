<?php

namespace App\Exception;

class BookCategoryNotEmpty extends \RuntimeException
{
    public function __construct(int $books)
    {
        parent::__construct(sprintf('%d books in this category', $books));
    }
}