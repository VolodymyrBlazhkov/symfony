<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
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
    use RepositoryModifyTrait;

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

    public function existBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug'=>$slug]);
    }

    public function existById(int $id, UserInterface $user): bool
    {
        return null !== $this->findOneBy(['id'=>$id, 'user' => $user]);
    }

    public function getBookById(int $id)
    {
        $book = $this->find($id);

        if ($book === null) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function saveBookFormatReference(BookToBookFormat $bookToBookFormat): void
    {
        $this->_em->persist($bookToBookFormat);
    }

    public function removeBookFormatReference(BookToBookFormat $bookToBookFormat): void
    {
        $this->_em->remove($bookToBookFormat);
    }
}
