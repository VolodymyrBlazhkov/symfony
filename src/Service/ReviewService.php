<?php

namespace App\Service;

use App\Entity\Review;
use App\Modal\Review as ReviewModal;
use App\Modal\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewService
{
    private const PAGE_LIMIT = 5;

    public function __construct(
        private ReviewRepository $reviewRepository,
        private RatingService $ratingService
    ) {
    }

    public function getReviewPageByBookId(int $id, int $page): ReviewPage
    {
        $offset = PaginationUtils::calcOffset($page, self::PAGE_LIMIT);
        $paginator = $this->reviewRepository->getPageByBookId($id, $offset, self::PAGE_LIMIT);
        $rating = $this->ratingService->calcReviewRatingForBook($id);
        return (new ReviewPage())
            ->setRating($rating->getRating())
            ->setTotal($rating->getTotal())
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(PaginationUtils::calcPages($rating->getTotal(), self::PAGE_LIMIT))
            ->setItems(array_map(
                [$this, 'map'],
                $paginator->getIterator()->getArrayCopy()
            ));
    }

    public function map(Review $review)
    {
        return (new ReviewModal())
            ->setId($review->getId())
            ->setRating($review->getRating())
            ->setCreateAt($review->getCreateAt()->getTimestamp())
            ->setAuthor($review->getAuthor())
            ->setContent($review->getContent());
    }
}