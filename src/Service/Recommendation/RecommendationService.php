<?php

namespace App\Service\Recommendation;

use App\Service\Recommendation\Exception\AccessDeniedException;
use App\Service\Recommendation\Exception\RequestException;
use App\Service\Recommendation\Modal\RecomendationResponse;
use mysql_xdevapi\CollectionModify;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class RecommendationService
{
    public function __construct(
        private HttpClientInterface $recommendationClient,
        private SerializerInterface $serializer
    ) {
    }

    public function getRecommendationByBookId(int $bookId): RecomendationResponse
    {
        try {
            //$response = $this->recommendationClient->request('GET', '/api/v1/book/' . $bookId . '/recommendations');
            return $this->serializer->deserialize(
                "{\"ts\":1111111,\"id\":123,\"recommendation\":[{\"id\":4}]}",
                RecomendationResponse::class,
                JsonEncoder::FORMAT
            );
        } catch (Throwable $exception) {
            if ($exception instanceof TransportExceptionInterface && Response::HTTP_FORBIDDEN === $exception->getCode()) {
                throw new AccessDeniedException($exception);
            }

            throw new RequestException($exception->getMessage(), $exception);
        }
    }
}