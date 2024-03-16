<?php

namespace App\EventListener;

use App\Modal\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListener
{
    public function __construct(
        private ExceptionMappingResolver $exceptionMappingResolver,
        private LoggerInterface $logger,
        private SerializerInterface $serializer
    ) {

    }

    public function __invoke(ExceptionEvent $event): void
    {
       $trowable = $event->getThrowable();
       $mapping = $this->exceptionMappingResolver->resolve(get_class($trowable));

       if (null === $mapping) {
           $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);
       }

       if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error(
                $trowable->getMessage(),
                [
                    'trace' => $trowable->getTraceAsString(),
                    'previous' => null !== $trowable->getPrevious() ? $trowable->getPrevious()->getMessage() : ''
                ]
            );
       }

       $massage = $mapping->isHidden() ? Response::$statusTexts[$mapping->getCode()] : $trowable->getMessage();
       $data = $this->serializer->serialize(
           new ErrorResponse($massage),
           JsonEncoder::FORMAT
       );

       $response =  new JsonResponse($data, $mapping->getCode(), [], true);
       $event->setResponse($response);
    }
}