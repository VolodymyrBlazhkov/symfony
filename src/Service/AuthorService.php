<?php

namespace App\Service;

use App\Entity\Book;
use App\Exception\BookExistWithSlugException;
use App\Modal\Author\BookListItem;
use App\Modal\Author\BookListResponse;
use App\Modal\Author\CreateBookRequest;
use App\Modal\Author\UploadImageResponse;
use App\Modal\IdResponse;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorService
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private UploadService $uploadService
    ) {
    }

    public function uploadImage(int $id, UploadedFile $file): UploadImageResponse
    {
        $book = $this->bookRepository->getBookById($id);
        $oldImage = $book->getImage();
        $link = $this->uploadService->uploadBookFile($id, $file);
        $book->setImage($link);
        $this->em->flush();

        if ($book->getImage() !== null) {
            $this->uploadService->deleteBookFile($book->getId(), basename($oldImage));
        }

        return new UploadImageResponse($link);
    }

    public function getBooks(UserInterface $user): BookListResponse
    {
        return new BookListResponse(
            array_map(
                [$this, 'map'],
                $this->bookRepository->findUserBooks($user)
            )
        );
    }

    public function createBook(CreateBookRequest $request, UserInterface $user): IdResponse
    {
        $slug = $this->slugger->slug($request->getTitle());

        if ($this->bookRepository->existBySlug($slug)) {
            throw new BookExistWithSlugException();
        }

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setSlug($slug)
            ->setMeap(false)
            ->setUser($user);

        $this->em->persist($book);
        $this->em->flush();

        return new IdResponse($book->getId());
    }
    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

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