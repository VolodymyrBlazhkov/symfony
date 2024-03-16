<?php

namespace App\Mapper;

use App\Entity\Book;
use App\Modal\BookDetails;
use App\Modal\BookListItem;
use App\Modal\RecommendedBook;

class BookMapper
{
    public static function mapDetails(Book $book, BookDetails|BookListItem $modal): BookDetails|BookListItem
    {
        return $modal->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp());
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
}