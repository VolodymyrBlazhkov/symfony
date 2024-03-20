<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Modal\BookCategoryUpdateRequest;
use App\Service\BookCategoryService;
use App\Service\RoleService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\ErrorResponse;
use App\Modal\IdResponse;

class AdminController extends AbstractController
{
    public function __construct(
        private RoleService $roleService,
        private BookCategoryService $bookCategoryService
    ) {
    }

    /**
     * @QA\Tag(name="Admin Api")
     * @QA\Response(
     *     response="200",
     *     description="Add role Author",
     * )
     * @QA\Response(
     *     response="404",
     *     description="User not exist",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path:'/api/v1/admin/grantAuthor/{userId}', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function grantAuthor(int $userId): Response
    {
        $this->roleService->grantAuthor($userId);
        return $this->json(null);

    }

    /**
     * @QA\Tag(name="Admin Api")
     * @QA\Response(
     *     response="200",
     *     description="Delete Category",
     * )
     * @QA\Response(
     *     response="404",
     *     description="Can't delete category",
     *     @Model(type=ErrorResponse::class)
     * )
     * @QA\Response(
     *      response="409",
     *      description="Category has books",
     *      @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path:'/api/v1/admin/deleteBookCategory/{id}', methods: ['DELETE'])]
    #[Security(name: 'Bearer')]
    public function deleteCategory(int $id): Response
    {
        $this->bookCategoryService->deleteCategory($id);
        return $this->json(null);

    }

    /**
     * @QA\Tag(name="Admin Api")
     * @QA\Response(
     *     response="200",
     *     description="Create Category",
     *     @Model(type=IdResponse::class)
     * )
     * @QA\Response(
     *     response="404",
     *     description="Can't create category",
     *     @Model(type=ErrorResponse::class)
     * )
     * @QA\RequestBody(@Model(type=BookCategoryUpdateRequest::class))
     */
    #[Route(path:'/api/v1/admin/createBookCategory', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function createCategory(#[RequestBody] BookCategoryUpdateRequest $request): Response
    {
        return $this->json($this->bookCategoryService->createCategory($request));

    }

    /**
     * @QA\Tag(name="Admin Api")
     * @QA\Response(
     *     response="200",
     *     description="Update Category",
     * )
     * @QA\Response(
     *     response="404",
     *     description="Can't update category",
     *     @Model(type=ErrorResponse::class)
     * )
     * @QA\RequestBody(@Model(type=BookCategoryUpdateRequest::class))
     */
    #[Route(path:'/api/v1/admin/updateBookCategory/{id}', methods: ['POST'])]
    #[Security(name: 'Bearer')]
    public function updateCategory(int $id, #[RequestBody] BookCategoryUpdateRequest $request): Response
    {
        $this->bookCategoryService->updateCategory($id, $request);
        return $this->json(null);

    }
}