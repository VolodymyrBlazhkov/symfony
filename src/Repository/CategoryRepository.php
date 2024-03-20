<?php

namespace App\Repository;

use App\Entity\Category;
use App\Exception\BookCategoryNotFoundException;
use App\Exception\BookNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findAllSortByTitle(): array
    {
        return $this->findBy([], ['title' => Criteria::ASC]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function existById(int $id)
    {
        return null !== $this->find($id);
    }

    public function getById(int $id): Category
    {
        $category = $this->find($id);

        if ($category === null) {
            throw new BookCategoryNotFoundException();
        }

        return $category;
    }

    public function countBooksInCategory(int $id): int
    {
        return $this->_em->createQuery('SELECT COUNT(b.id) FROM App\Entity\Book b WHERE :catId MEMBER OF b.categories')
            ->setParameter('catId', $id)
            ->getSingleScalarResult();
    }

    public function existBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug'=>$slug]);
    }

}
