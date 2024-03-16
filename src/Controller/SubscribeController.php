<?php

namespace App\Controller;


use App\Modal\SubscriberRequest;
use App\Service\SubscribeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as QA;
use App\Attribute\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Modal\BookListResponse;
use App\Modal\ErrorResponse;

class SubscribeController extends AbstractController
{
    public function __construct(private SubscribeService $subscribeService)
    {
    }

    /**
     * @QA\Response(
     *     response="200",
     *     description="Subscribe",
     *     @Model(type=BookListResponse::class)
     * )
     * @QA\RequestBody(@Model(type=SubscriberRequest::class))
     * @QA\Response(
     *     response="404",
     *     description="book category not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path:'/api/v1/subscribe', methods: ['POST'])]
    public function subscribe(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        $this->subscribeService->subscribe($subscriberRequest);

        return $this->json(null);
    }
}