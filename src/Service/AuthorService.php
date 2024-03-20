<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Exception\BookExistWithSlugException;
use App\Mapper\BookMapper;
use App\Modal\Author\BookDetails;
use App\Modal\Author\BookFormatOptions;
use App\Modal\Author\BookListItem;
use App\Modal\Author\BookListResponse;
use App\Modal\Author\CreateBookRequest;
use App\Modal\Author\UpdateBookRequest;
use App\Modal\Author\UploadImageResponse;
use App\Modal\IdResponse;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookFormatRepository $bookFormatRepository,
        private CategoryRepository $categoryRepository,
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
        $this->bookRepository->commit();

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
        $slug = $this->validateSlug($request->getTitle());

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setSlug($slug)
            ->setMeap(false)
            ->setUser($user);
        $this->bookRepository->saveAndCommit($book);

        return new IdResponse($book->getId());
    }

    public function getBook(int $id): BookDetails
    {
        $book = $this->bookRepository->getBookById($id);

        $bookDetail = (new BookDetails)
            ->setIsbn($book->getIsbn())
            ->setDescription($book->getDescription())
            ->setFormats(BookMapper::mapFormats($book))
            ->setCategories(BookMapper::mapCategories($book));
        $book->setMeap(false);
        return BookMapper::mapDetails($book, $bookDetail);
    }

    public function updateBook(int $id, UpdateBookRequest $updateBookRequest): void
    {
        $book = $this->bookRepository->getBookById($id);
        $title = $updateBookRequest->getTitle();

        if(!empty($title)) {
            $book->setTitle($title)->setSlug($this->validateSlug($title));
        }

        $formats = array_map(function (BookFormatOptions $options) use ($book): BookToBookFormat {
            $format = (new BookToBookFormat())
                ->setPrice($options->getPrice())
                ->setDiscountPercent($options->getDiscountPercent())
                ->setBook($book)
                ->setFormat($this->bookFormatRepository->getById($options->getId()));

            $this->bookRepository->saveBookFormatReference($format);

            return $format;
        },  $updateBookRequest->getFormats() );

        foreach ($book->getFormats() as $format) {
            $this->bookRepository->removeBookFormatReference($format);
        }

        $book->setAuthors($updateBookRequest->getAuthors())
            ->setIsbn($updateBookRequest->getIsbn())
            ->setDescription($updateBookRequest->getDescription())
            ->setCategories(new ArrayCollection(
                $this->categoryRepository->findByBookCategoriesIds($updateBookRequest->getCategories())
            ))
            ->setFormats(new ArrayCollection($formats));

        $this->bookRepository->commit();
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);
        $this->bookRepository->removeAndCommit($book);
    }

    public function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setSlug($book->getSlug())
            ->setTitle($book->getTitle())
            ->setImage($book->getImage());
    }

    private function validateSlug(string $title): string
    {
        $slug = $this->slugger->slug($title);

        if ($this->bookRepository->existBySlug($slug)) {
            throw new BookExistWithSlugException();
        }

        return $slug;
    }
}