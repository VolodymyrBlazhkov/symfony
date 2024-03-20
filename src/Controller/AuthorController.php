<?php

namespace App\Controller;


use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Modal\Author\CreateBookRequest;
use App\Modal\Author\PublishBookRequest;
use App\Service\AuthorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use OpenApi\Annotations as QA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\Author\BookListResponse;
use App\Modal\ErrorResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Modal\Author\UploadImageResponse;

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
    #[Security(name: 'Bearer')]
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
    #[Security(name: 'Bearer')]
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
    #[Security(name: 'Bearer')]
    public function createBook(#[RequestBody] CreateBookRequest $request): Response
    {
        return $this->json($this->authorService->createBook($request));
    }

    /**
     * @QA\Tag(name="Author Api")
     * @QA\Response(
     *     response="200",
     *     description="Publish book"
     * )
     * @QA\Response(
     *      response="404",
     *      description="books exist with slug",
     *      @Model(type=ErrorResponse::class)
     *  )
     * @QA\RequestBody(@Model(type=PublishBookRequest::class))
     */
    #[Route(path:'/api/v1/author/book/{id}/publish', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function publishBook(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->authorService->publish($id, $request);
        return $this->json(null);
    }

    /**
     * @QA\Tag(name="Author Api")
     * @QA\Response(
     *     response="200",
     *     description="Un Publish book"
     * )
     */
    #[Route(path:'/api/v1/author/book/{id}/unpublish', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function unPublishBook(int $id): Response
    {
        $this->authorService->unPublish($id);
        return $this->json(null);
    }


    /**
     * @QA\Tag(name="Author Api")
     * @QA\Response(
     *     response="200",
     *     description="Upload book image",
     *     @Model(type=UploadImageResponse::class)
     * )
     * @QA\Response(
     *      response="404",
     *      description="Validation faild",
     *      @Model(type=ErrorResponse::class)
     *  )
     * @OA\RequestBody(
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  description="file to upload",
     *                  property="cover",
     *                  type="string",
     *                  format="binary",
     *              )
     *          )
     *     )
     * )
     */
    #[Route(path:'/api/v1/author/book/{id}/uploadImage', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function uploadImage(
        int $id,
        #[RequestFile(field: 'cover', constraints: [
            new NotBlank(),
            new Image(maxSize: '1M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
        ])] UploadedFile $file): Response
    {
        return $this->json($this->authorService->uploadImage($id, $file));
    }
}