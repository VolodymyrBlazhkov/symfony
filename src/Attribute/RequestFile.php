<?php

namespace App\Attribute;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class RequestFile
{
    public function __construct(
        private string $field,
        private array $constraints = []
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }

}