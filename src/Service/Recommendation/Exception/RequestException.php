<?php

namespace App\Service\Recommendation\Exception;

final class RequestException extends RecommendationException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }
}