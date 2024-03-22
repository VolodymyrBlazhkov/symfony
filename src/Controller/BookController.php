<?php

namespace App\Controller;


use App\Security\Vouter\AuthorBookVouter;
use App\Service\BookContentService;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\BookListResponse;
use App\Modal\ErrorResponse;
use App\Modal\BookDetails;
use App\Modal\BookChapterContentPage;

class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService,
        private BookContentService $bookContentService
    ) {
    }

    /**
     * @QA\Tag(name="Book")
     * @QA\Response(
     *     response="200",
     *     description="Returns books by category",
     *     @Model(type=BookListResponse::class)
     * )
     * @QA\Response(
     *     response="404",
     *     description="book category not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path:'/api/v1/category/{id}/books', methods: ['GET'])]
    public function categories(int $id): Response
    {
        return $this->json($this->bookService->getBooksByCategoty($id));
    }


    /**
     * @QA\Tag(name="Book")
     * @QA\Response(
     *     response="200",
     *     description="Returns book detais",
     *     @Model(type=BookDetails::class)
     * )
     * @QA\Response(
     *     response="404",
     *     description="book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path:'/api/v1/book/{id}', methods: ['GET'])]
    public function bookById(int $id): Response
    {
        return $this->json($this->bookService->getBookById($id));
    }

    /**
     * @QA\Tag(name="Book")
     * @QA\Parameter(name="page", in="query", description="Page Number", @QA\Schema(type="integer"))
     * @QA\Response(
     *     response="200",
     *     description="Get Chapter publick content ",
     *     @Model(type=BookChapterContentPage::class)
     * )
     * @QA\Response(
     *      response="404",
     *      description="books not found",
     *      @Model(type=ErrorResponse::class)
     *  )
     */
    #[Route(path:'/api/v1/book/{bookId}/chapters/{chapterId}/publicContent', methods: ['GET'])]
    public function publicContent(Request $request, int $bookId, int $chapterId): Response
    {
        return $this->json(
            $this->bookContentService->getPublishedContent(
                $chapterId,
                $request->query->get('page', 1)
            )
        );
    }
}