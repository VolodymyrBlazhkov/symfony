<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Entity\Category;
use App\Exception\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Modal\BookCategoryListItem;
use App\Modal\BookDetails;
use App\Modal\BookFormat;
use App\Modal\BookListItem;
use App\Modal\BookListResponse;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\Recommendation\Modal\RecommendationItem;
use App\Service\Recommendation\RecommendationService;
use Doctrine\Common\Collections\Collection;
use Exception;
use Psr\Log\LoggerInterface;

class BookService
{
    public function __construct(
        private BookRepository        $bookRepository,
        private CategoryRepository    $categoryRepository,
        private RatingService         $ratingService,
        private RecommendationService $recommendationService,
        private LoggerInterface       $logger
    ) {
    }

    public function getBooksByCategoty(int $categoryId): BookListResponse
    {
        if (!$this->categoryRepository->existById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::mapDetails($book, new BookListItem()),
            $this->bookRepository->findBooksPublishedByCategoryId($categoryId)
        ));
    }

    public function getRecommendations(int $id): array
    {
        $ids = array_map(
            fn (RecommendationItem $recommendationItem) => $recommendationItem->getId(),
            $this->recommendationService->getRecommendationByBookId($id)->getRecommendation()
        );
        return array_map([
                BookMapper::class,
                'mapRecommended'
            ],
            $this->bookRepository->findBooksByIds($ids)
        );
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getPublishedById($id);
        $categories = $book->getCategories()
            ->map(fn (Category $bookCategory) => new BookCategoryListItem(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
            ));

        $recommendation = [];

        try {
            $recommendation = $this->getRecommendations($id);
        } catch (Exception $exception) {
            $this->logger->error('error while request', []);
        }
        $rating = $this->ratingService->calcReviewRatingForBook($id);

        return BookMapper::mapDetails($book, new BookDetails())
            ->setRating($rating->getRating())
            ->setReviews($rating->getTotal())
            ->setRecommendation($recommendation)
            ->setFormats($this->mapFormats($book->getFormats())->toArray())
            ->setCategories($categories->toArray());
    }

    private function mapFormats(Collection $formats)
    {
        return $formats->map(fn (BookToBookFormat $bookToBookFormat) => (new BookFormat())
            ->setId($bookToBookFormat->getFormat()->getId())
            ->setTitle($bookToBookFormat->getFormat()->getTitle())
            ->setDescription($bookToBookFormat->getFormat()->getDescription())
            ->setComment($bookToBookFormat->getFormat()->getComment())
            ->setPrice($bookToBookFormat->getPrice())
            ->setDiscountProcent($bookToBookFormat->getDiscountPercent())
        );
    }

}