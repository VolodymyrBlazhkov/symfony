<?php

namespace App\Service;

use App\Entity\Book;
use App\Exception\BookExistWithSlugException;
use App\Modal\Author\BookListItem;
use App\Modal\Author\BookListResponse;
use App\Modal\Author\CreateBookRequest;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use RectorPrefix202403\SebastianBergmann\Diff\TimeEfficientLongestCommonSubsequenceCalculator;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorService
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private Security $security
    ) {
    }


    public function getBooks(): BookListResponse
    {
        $user = $this->security->getUser();

        return new BookListResponse(
            array_map(
                [$this, 'map'],
                $this->bookRepository->findUserBooks($user)
            )
        );
    }

    public function createBook(CreateBookRequest $request)
    {
        $slug = $this->slugger->slug($request->getTitle());

        if ($this->bookRepository->existBySlug($slug)) {
            throw new BookExistWithSlugException();
        }

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setSlug($slug)
            ->setMeap(false)
            ->setUser($this->security->getUser());

        $this->em->persist($book);
        $this->em->flush();
    }
    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getUserBookId($id, $this->security->getUser());

        $this->em->remove($book);
        $this->em->flush();
    }


    public function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setSlug($book->getSlug())
            ->setTitle($book->getTitle())
            ->setImage($book->getImage());
    }
}