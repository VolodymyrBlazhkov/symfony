<?php

namespace App\Controller;


use App\Modal\SignUpRequest;
use App\Service\SignUpService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use App\Attribute\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\ErrorResponse;
use App\Modal\IdResponse;

class AuthController extends AbstractController
{
    public function __construct(private SignUpService $signUpService)
    {
    }

    /**
     * @QA\Response(
     *     response="200",
     *     description="Create user",
     *     @QA\JsonContent(
     *         @QA\Property(property="token", type="string"),
     *         @QA\Property(property="refresh_token", type="string")
     *     )
     * )
     * @QA\RequestBody(@Model(type=SignUpRequest::class))
     * @QA\Response(
     *     response="409",
     *     description="User exist",
     *     @Model(type=ErrorResponse::class)
     * )
     * @QA\Response(
     *      response="400",
     *      description="Validation failed",
     *      @Model(type=ErrorResponse::class)
     *  )
     */
    #[Route(path:'/api/v1/auth/signUp', methods: ['POST'])]
    public function auth(#[RequestBody] SignUpRequest $signUpRequest): Response
    {
        return $this->signUpService->signUp($signUpRequest);
    }
}