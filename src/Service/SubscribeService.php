<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberExistException;
use App\Modal\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscribeService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function subscribe(SubscriberRequest $subscriberRequest): void
    {
        if ($this->subscriberRepository->existByEmail($subscriberRequest->getEmail())) {
            throw new SubscriberExistException();
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($subscriberRequest->getEmail());

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}