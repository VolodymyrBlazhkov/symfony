<?php

namespace App\Modal\Author;

class BookListResponse
{
    /**
     * @var BookListItem[]
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
     * @return BookListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BookListItem[] $items
     * @return BookListResponse
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }
}