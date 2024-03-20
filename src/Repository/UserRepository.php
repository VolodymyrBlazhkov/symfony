<?php

namespace App\Repository;

use App\Entity\User;
use App\Exception\UserNotExistException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function existByEmail(string $email)
    {
        return null !== $this->findOneBy(['email' => $email]);
    }

    public function getUser(int $id): ?User
    {
        $user = $this->find($id);

        if ($user === null) {
            throw new UserNotExistException();
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
    }

    public function commit(): void
    {
        $this->_em->flush();
    }

    public function saveAndCommit(User $user): void
    {
        $this->save($user);
        $this->commit();
    }
}
