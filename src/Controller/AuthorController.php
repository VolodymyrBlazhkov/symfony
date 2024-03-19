<?php

namespace App\Controller;


use App\Attribute\RequestBody;
use App\Modal\Author\CreateBookRequest;
use App\Service\AuthorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\Author\BookListResponse;
use App\Modal\ErrorResponse;

class AuthorController extends AbstractController
{
    public function __construct(private AuthorService $authorService)
    {
    }

    /**
     * @QA\Tag(name="Author Api")
     * @QA\Response(
     *     response="200",
     *     description="Returns books",
     *     @Model(type=BookListResponse::class)
     * )
     */
    #[Route(path:'/api/v1/author/books', methods: ['GET'])]
    public function books(): Response
    {
        return $this->json($this->authorService->getBooks());
    }
    /**
     * @QA\Tag(name="Author Api")
     * @QA\Response(
     *     response="200",
     *     description="DElete book"
     * )
     * @QA\Response(
     *      response="404",
     *      description="books not found",
     *      @Model(type=ErrorResponse::class)
     *  )
     *
     */
    #[Route(path:'/api/v1/author/book/{id}', methods: ['DELETE'])]
    public function deleteBooks(int $id): Response
    {
        $this->authorService->deleteBook($id);
        return $this->json(null);
    }

    /**
     * @QA\Tag(name="Author Api")
     * @QA\Response(
     *     response="200",
     *     description="Create book"
     * )
     * @QA\Response(
     *      response="404",
     *      description="books exist with slug",
     *      @Model(type=ErrorResponse::class)
     *  )
     * @QA\RequestBody(@Model(type=CreateBookRequest::class))
     */
    #[Route(path:'/api/v1/author/book', methods: ['POST'])]
    public function createBook(#[RequestBody] CreateBookRequest $request): Response
    {
        $this->authorService->createBook($request);
        return $this->json(null);
    }
}