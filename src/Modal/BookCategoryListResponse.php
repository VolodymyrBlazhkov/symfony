<?php

namespace App\Modal;

class BookCategoryListResponse
{
    /**
     * @var BookCategoryListItem[]
     */
    private array $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return BookCategoryListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BookCategoryListItem[] $items
     * @return BookCategoryListResponse
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }
}