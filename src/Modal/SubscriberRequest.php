<?php

namespace App\Modal;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriberRequest
{
    #[Email]
    #[NotBlank]
    private string $email;

    #[IsTrue]
    #[NotBlank]
    private bool $agreed;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isAgreed(): bool
    {
        return $this->agreed;
    }

    public function setAgreed(bool $agreed): self
    {
        $this->agreed = $agreed;

        return $this;
    }
}