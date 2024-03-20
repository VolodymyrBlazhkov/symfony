<?php

namespace App\Service;

use App\Modal\Author\PublishBookRequest;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class BookPublishService
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function publish(int $id, PublishBookRequest $publishBookRequest): void
    {
        $this->setPublicationDate($id, $publishBookRequest->getDate());
    }

    public function unPublish(int $id): void
    {
        $this->setPublicationDate($id, null);
    }

    private function setPublicationDate(int $id, ?\DateTimeInterface $dateTime): void
    {
        $book = $this->bookRepository->getBookById($id);
        $book->setPublicationDate($dateTime);

        $this->em->flush();
    }
}