<?php

namespace App\Security\Vouter;

use App\Repository\BookRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AuthorBookVouter extends Voter
{
    public const BOOK_PUBLISH = 'BOOK_PUBLISH';

    public function __construct(private BookRepository $bookRepository)
    {
    }

    protected function supports(string $attribute, $subject)
    {
        if (self::BOOK_PUBLISH !== $attribute) {
            return false;
        }

        return $subject;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return $this->bookRepository->existById((int) $subject, $token->getUser());
    }
}