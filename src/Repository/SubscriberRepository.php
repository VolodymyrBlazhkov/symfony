<?php

namespace App\Repository;

use App\Entity\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscriber>
 *
 * @method Subscriber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriber[]    findAll()
 * @method Subscriber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function existByEmail(string $email)
    {
        return null !== $this->findOneBy(['email' => $email]);
    }

    public function save(Subscriber $subscriber): void
    {
        $this->_em->persist($subscriber);
    }

    public function commit(): void
    {
        $this->_em->flush();
    }

    public function saveAndCommit(Subscriber $subscriber): void
    {
        $this->save($subscriber);
        $this->commit();
    }
}
