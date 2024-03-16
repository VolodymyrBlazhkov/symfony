<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\userExistException;
use App\Modal\IdResponse;
use App\Modal\SignUpRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
        private AuthenticationSuccessHandler $successHandler
    ) {
    }

    public function signUp(SignUpRequest $signUpRequest): Response
    {
        if ($this->userRepository->existByEmail($signUpRequest->getEmail())) {
            throw new userExistException();
        }
        $user = (new User())
            ->setRoles(['ROLE_USER'])
            ->setLastName($signUpRequest->getLastName())
            ->setFirstName($signUpRequest->getFirstName())
            ->setEmail($signUpRequest->getEmail());
        $user->setPassword($this->passwordHasher->hashPassword($user, $signUpRequest->getPassword()));

        $this->em->persist($user);
        $this->em->flush();

        return $this->successHandler->handleAuthenticationSuccess($user);
    }
}