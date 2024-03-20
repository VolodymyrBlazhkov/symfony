<?php

namespace App\Mapper;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Entity\Category;
use App\Modal\Author\BookDetails as AuthorBookDetails;
use App\Modal\BookCategoryListItem;
use App\Modal\BookDetails;
use App\Modal\BookFormat;
use App\Modal\BookListItem;
use App\Modal\RecommendedBook;

class BookMapper
{
    public static function mapDetails(Book $book, BookDetails|BookListItem|AuthorBookDetails $modal): BookDetails|BookListItem|AuthorBookDetails
    {
        $date = $book->getPublicationDate();
        if ($date !== null) {
            $date = $date->getTimestamp();
        }
        return $modal->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($date);
    }

    public static function mapRecommended(Book $book): RecommendedBook
    {
        $desc = $book->getDescription();
        $desc = strlen($desc) > 150 ? substr($desc, 0, 150) . ' ...' : $desc;

        return (new RecommendedBook())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setImage($book->getImage())
            ->setSlug($book->getSlug())
            ->setShortDescription($desc);
    }

    public static function mapCategories(Book $book): array
    {
        return $book->getCategories()
            ->map(fn (Category $bookCategory) => new BookCategoryListItem(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
            ))->toArray();
    }

    /**
     * @param Book $book
     * @return BookFormat[]
     */
    public static function mapFormats(Book $book): array
    {
        return $book->getFormats()
            ->map(fn (BookToBookFormat $bookToBookFormat) => (new BookFormat())
                ->setId($bookToBookFormat->getFormat()->getId())
                ->setTitle($bookToBookFormat->getFormat()->getTitle())
                ->setDescription($bookToBookFormat->getFormat()->getDescription())
                ->setComment($bookToBookFormat->getFormat()->getComment())
                ->setPrice($bookToBookFormat->getPrice())
                ->setDiscountProcent($bookToBookFormat->getDiscountPercent())
            )->toArray();
    }
}