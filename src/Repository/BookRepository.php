<?php

namespace App\Repository;

use App\Entity\Book;
use App\Exception\BookNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param int $id
     * @return Book[]
     */
    public function findBooksPublishedByCategoryId(int $id): array
    {
        $query = $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE :categoryId MEMBER OF b.categories AND b.publicationDate IS NOT NULL');
        $query->setParameter("categoryId", $id);

        return $query->getResult();
    }

    public function getPublishedById(int $id): Book
    {
        $query = $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE b:id = :id AND b.publicationDate IS NOT NULL');
        $query->setParameter("id", $id);
        $book = $query->getOneOrNullResult();
        if (null === $book) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function findBooksByIds(array $ids): array
    {
        $query = $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE b:id MEMBER OF :ids AND b.publicationDate IS NOT NULL');
        $query->setParameter("ids", $ids);
        return $query->getResult();
    }

    public function findUserBooks(UserInterface $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    public function getUserBookId(int $id, UserInterface $user): Book
    {
        $book =  $this->findOneBy(['id' => $id, 'user' => $user]);

        if ($book === null) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function existBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug'=>$slug]);
    }
}
