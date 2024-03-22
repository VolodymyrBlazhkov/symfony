<?php

namespace App\Repository;

use App\Entity\BookContent;
use App\Entity\BookFormat;
use App\Exception\BookCcontentNotFoundException;
use App\Exception\BookFormatNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookContent>
 *
 * @method BookContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookContent[]    findAll()
 * @method BookContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookContentRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookContent::class);
    }

    public function getById(int $id): BookFormat
    {
        $content = $this->find($id);

        if ($content === null) {
            throw new BookCcontentNotFoundException();
        }

        return $content;
    }


    public function getPageByChapterId(int $id, bool $onlyPublished, int $offset, int $limit)
    {
        $query = implode(
            '',
            array_filter(
                [
                    'SELECT b FROM App\Entity\BookContent b WHERE b.chapter = :id',
                    $onlyPublished ? ' AND b.isPublished = true' : null,
                    ' ORDER BY b.id ASC'
                ]
            )
        );

        $query =  $this->_em->createQuery($query)
            ->setParameter('id', $id)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function countByChapterId(int $id, bool $onlyPublished): int
    {
        $condition = ['chapter' => $id];
        if ($onlyPublished) {
            $condition['isPublished'] = true;
        }

        return $this->count($condition);
    }
}
