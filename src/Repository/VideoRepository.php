<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);
        $this->paginator = $paginator;
    }

    public function findByChildIds(int $page , array  $ids, ?string $sortMethod)
    {
       // $sortMethod = $sortMethod != 'rating' ? $sortMethod: 'ASC';

        if ($sortMethod != 'rating') {
            $qb = $this->createQueryBuilder('v')
                ->andWhere('v.category IN (:ids)')
                ->leftJoin('v.comments', 'c')
                ->leftJoin('v.userThatLike', 'l')
                ->leftJoin('v.userThatDontLike', 'd')
                ->addSelect('c', 'l', 'd')
                ->orderBy('v.title', $sortMethod)
                ->setParameter('ids', $ids)
                ->getQuery();
        }
        else {
            $qb = $this->createQueryBuilder('v')
                ->addSelect('COUNT(l) AS HIDDEN likes')
                ->leftJoin('v.userThatLike', 'l')
                ->andWhere('v.category IN (:ids)')
                ->setParameter('ids', $ids)
                ->groupBy('v')
                ->orderBy('likes', 'DESC')
                ->getQuery();
        }

        return $this->paginator->paginate($qb, $page, Video::PER_PAGE);
    }


    public function findByTitle(string $query, int $page, ?string $sortMethod)
    {
        $qb = $this->createQueryBuilder('v');

        if ($sortMethod != 'rating') {

            $searchTerms = $this->prepareQuery($query);

            foreach ($searchTerms as $key => $term){
                $qb->orWhere('v.title LIKE :t_' . $key)
                    ->setParameter('t_'. $key, '%' . trim($term). '%');
            }

            $qb->orderBy('v.title', $sortMethod)
                ->leftJoin('v.comments', 'c')
                ->leftJoin('v.userThatLike', 'l')
                ->leftJoin('v.userThatDontLike', 'd')
                ->addSelect('c', 'l', 'd')
                ->getQuery();

        } else {
            $qb->addSelect('COUNT(l) AS HIDDEN likes', 'c')
                ->leftJoin('v.userThatLike', 'l')
                ->leftJoin('v.comments', 'c')
                ->groupBy('v', 'c')
                ->orderBy('likes', 'DESC')
                ->getQuery();
        }

        return $this->paginator->paginate($qb, $page, Video::PER_PAGE);

    }

    private function prepareQuery(string $query): array
    {
        return explode(' ', $query);
    }


    public function videoDetails(int $id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.comments', 'c')
            ->leftJoin('c.user', 'u')
            ->addSelect('c', 'u')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }



    // /**
    //  * @return Video[] Returns an array of Video objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
