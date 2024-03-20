<?php

namespace App\Modal\Author;

class UploadImageResponse
{
    public function __construct(private string $link)
    {
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

}