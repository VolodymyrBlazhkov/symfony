<?php

namespace App\Modal;

use OpenApi\Annotations as QA;

class ErrorResponse
{
    public function __construct(private string $massage, private mixed $details = null)
    {
    }

    public function getMassage(): string
    {
        return $this->massage;
    }

    public function setMassage(string $massage): self
    {
        $this->massage = $massage;

        return $this;
    }

    /**
     * @QA\Property(type="object")
     */
    public function getDetails(): mixed
    {
        return $this->details;
    }


    public function setDetails(mixed $details): self
    {
        $this->details = $details;

        return $this;
    }
}