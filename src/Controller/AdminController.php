<?php

namespace App\Controller;

use App\Service\RoleService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\ErrorResponse;

class AdminController extends AbstractController
{
    public function __construct(private RoleService $roleService)
    {
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
}