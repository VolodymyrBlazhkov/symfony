<?php

namespace App\Controller;

use App\Service\ReviewService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\BookListResponse;
use App\Modal\ErrorResponse;
use App\Modal\BookDetails;
use App\Modal\ReviewPage;

class ReviewController extends AbstractController
{
    public function __construct(private ReviewService $reviewService)
    {
    }

    /**
     * @QA\Parameter(name="page", in="query", description="Page Number", @QA\Schema(type="integer"))
     * @QA\Response(
     *     response="200",
     *     description="Returns books by category",
     *     @Model(type=ReviewPage::class)
     * )
     * @QA\Response(
     *     response="404",
     *     description="returns book review",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path:'/api/v1/book/{id}/reviews', methods: ['GET'])]
    public function reviews(int $id, Request $request): Response
    {
        return $this->json($this->reviewService->getReviewPageByBookId(
            $id,
            $request->query->get('page', 1)
        ));
    }
}