<?php

namespace App\Service\ExceptionHandler;

class ExceptionMapping
{
    public function __construct(
        private int $code,
        private bool $hidden,
        private bool $loggable
    ) {
    }

    public static function fromCode(int $code): self
    {
        return new self($code, true, false);
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isLoggable(): bool
    {
        return $this->loggable;
    }

    public function setLoggable(bool $loggable): self
    {
        $this->loggable = $loggable;

        return $this;
    }
}