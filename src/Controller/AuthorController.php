<?php

namespace App\Controller;


use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Modal\Author\CreateBookChapterContentRequest;
use App\Modal\Author\CreateBookChapterRequest;
use App\Modal\Author\CreateBookRequest;
use App\Modal\Author\PublishBookRequest;
use App\Modal\Author\UpdateBookChapterRequest;
use App\Modal\Author\UpdateBookChapterSortRequest;
use App\Modal\Author\UpdateBookRequest;
use App\Modal\BookChapterContentPage;
use App\Modal\BookChapterTreeResponse;
use App\Modal\IdResponse;
use App\Service\AuthorBookChaperService;
use App\Service\AuthorService;
use App\Service\BookContentService;
use App\Service\BookPublishService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody as QARequestBody;
use OpenApi\Attributes\Response as QAResponse;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\Author\BookListResponse;
use App\Modal\ErrorResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Modal\Author\UploadImageResponse;
use App\Security\Vouter\AuthorBookVouter;
use App\Modal\Author\BookDetails;

class AuthorController extends AbstractController
{
    public function __construct(
        private AuthorService $authorService,
        private BookPublishService $bookPublish,
        private AuthorBookChaperService $authorBookChaperService,
        private BookContentService $bookContentService
    ) {
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Delete book", attachables: [new Model(type: BookListResponse::class)])]
    #[Route(path:'/api/v1/author/books', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    public function books(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorService->getBooks($user));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Delete book")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[Route(path:'/api/v1/author/book/{id}', methods: ['DELETE'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function deleteBooks(int $id): Response
    {
        $this->authorService->deleteBook($id);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Create book")]
    #[QAResponse(response: 404, description: "books exist with slug", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: CreateBookRequest::class)])]
    #[Route(path:'/api/v1/author/book', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function createBook(#[RequestBody] CreateBookRequest $request, #[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorService->createBook($request, $user));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Publish book")]
    #[QAResponse(response: 404, description: "books exist with slug", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: PublishBookRequest::class)])]
    #[Route(path:'/api/v1/author/book/{id}/publish', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function publishBook(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->bookPublish->publish($id, $request);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Un Publish book")]
    #[Route(path:'/api/v1/author/book/{id}/unpublish', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function unPublishBook(int $id): Response
    {
        $this->bookPublish->unPublish($id);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Upload book image", attachables: [new Model(type: UploadImageResponse::class)])]
    #[QAResponse(response: 404, description: "Validation faild", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(content: [
        new MediaType(
            mediaType: 'multipart/form-data',
            schema: new Schema(
                properties: [new Property(
                    property: 'cover',
                    description: 'file to upload',
                    type: 'string',
                    format: 'binary'
                )]
            )
        )
    ])]
    #[Route(path:'/api/v1/author/book/{id}/uploadImage', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function uploadImage(
        int $id,
        #[RequestFile(field: 'cover', constraints: [
            new NotBlank(),
            new Image(maxSize: '1M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
        ])] UploadedFile $file): Response
    {
        return $this->json($this->authorService->uploadImage($id, $file));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Update book")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: UpdateBookRequest::class)])]
    #[Route(path:'/api/v1/author/updateBook/{id}', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function updateBook(int $id, #[RequestBody] UpdateBookRequest $request): Response
    {
        $this->authorService->updateBook($id, $request);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Get book", attachables: [new Model(type: BookDetails::class)])]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[Route(path:'/api/v1/author/book/{id}', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function book(int $id): Response
    {
        return $this->json( $this->authorService->getBook($id));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "create Book Chapter", attachables: [new Model(type: IdResponse::class)])]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: CreateBookChapterRequest::class)])]
    #[Route(path:'/api/v1/author/book/{id}/chapter', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function createBookChapter(#[RequestBody] CreateBookChapterRequest $request, int $id): Response
    {
        return $this->json($this->authorBookChaperService->createChapter($request, $id));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Sort Book Chapter")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: UpdateBookChapterSortRequest::class)])]
    #[Route(path:'/api/v1/author/book/{id}/chapterSort', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function updateBookChapterSort(#[RequestBody] UpdateBookChapterSortRequest $request, int $id): Response
    {
        $this->authorBookChaperService->updateChapterSort($request);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Update Chapter")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: UpdateBookChapterRequest::class)])]
    #[Route(path:'/api/v1/author/book/{id}/updateChapter', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function updateBookChapter(#[RequestBody] UpdateBookChapterRequest $request, int $id): Response
    {
        $this->authorBookChaperService->updateChapter($request);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Get TreeChapter", attachables: [new Model(type: BookChapterTreeResponse::class)])]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[Route(path:'/api/v1/author/book/{id}/chapters', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'id')]
    public function chapters(int $id): Response
    {
        return $this->json($this->authorBookChaperService->getChaptersTree($id));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Delete Chapter")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[Route(path:'/api/v1/author/book/{bookId}/deleteChapter/{id}', methods: ['DELETE'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'bookId')]
    public function deleteChapter(int $id, int $bookId): Response
    {
        $this->authorBookChaperService->deleteChapter($id);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "create Book Chapter content", attachables: [new Model(type: IdResponse::class)])]
    #[QAResponse(response: 404, description: "books content not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: CreateBookChapterContentRequest::class)])]
    #[Route(path:'/api/v1/author/book/{bookId}/createChapterContent/{chapterId}/content', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'bookId')]
    public function createBookChapterContent(#[RequestBody] CreateBookChapterContentRequest $request, int $bookId, int $chapterId): Response
    {
        return $this->json($this->bookContentService->createContent($request, $chapterId));
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Delete Chapter content")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[Route(path:'/api/v1/author/book/{bookId}/deleteChapterContent/{chapterId}/content/{id}', methods: ['DELETE'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'bookId')]
    public function deleteChapterContent(int $chapterId, int $bookId,  int $id): Response
    {
        $this->bookContentService->deleteContent($id);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[QAResponse(response: 200, description: "Update Chapter content")]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[QARequestBody(attachables: [new Model(type: CreateBookChapterContentRequest::class)])]
    #[Route(path:'/api/v1/author/book/{bookId}/updateChapterContent/{chapterId}/content/{id}', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'bookId')]
    public function updateBookChapterContent(#[RequestBody] CreateBookChapterContentRequest $request, int $bookId, int $chapterId, int $id): Response
    {
        $this->bookContentService->updateContent($request, $id);
        return $this->json(null);
    }

    #[Tag('Author Api')]
    #[Parameter(name: 'page', description: 'Page Number', in: 'query', schema: new Schema(type: 'integer'))]
    #[QAResponse(response: 200, description: "Get Chapter content", attachables: [new Model(type: BookChapterContentPage::class)])]
    #[QAResponse(response: 404, description: "books not found", attachables: [new Model(type: ErrorResponse::class)])]
    #[Route(path:'/api/v1/author/book/{bookId}/chapters/{chapterId}/content', methods: ['GET'])]
    #[Security(name: 'Bearer')]
    #[IsGranted(AuthorBookVouter::BOOK_PUBLISH, subject: 'bookId')]
    public function content(Request $request, int $bookId, int $chapterId): Response
    {
        return $this->json(
            $this->bookContentService->getAllContent(
                $chapterId,
                $request->query->get('page', 1)
            )
        );
    }
}