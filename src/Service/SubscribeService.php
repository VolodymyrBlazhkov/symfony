<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberExistException;
use App\Modal\SubscriberRequest;
use App\Repository\SubscriberRepository;

class SubscribeService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository
    ) {
    }

    public function subscribe(SubscriberRequest $subscriberRequest): void
    {
        if ($this->subscriberRepository->existByEmail($subscriberRequest->getEmail())) {
            throw new SubscriberExistException();
        }

        $this->subscriberRepository->saveAndCommit((new Subscriber())->setEmail($subscriberRequest->getEmail()));
    }
}