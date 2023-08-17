<?php

namespace App\Repository;

use App\Entity\Color;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Color>
 *
 * @method Color|null find($id, $lockMode = null, $lockVersion = null)
 * @method Color|null findOneBy(array $criteria, array $orderBy = null)
 * @method Color[]    findAll()
 * @method Color[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColorRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface 
        )
    {
        parent::__construct($registry, Color::class);
    }

    public function	findPaginateColors(int $page): PaginationInterface {	
        
        $data = $this->createQueryBuilder('co')	
      	    ->orderBy('co.name', 'ASC')	
      	    ->getQuery()
            ->getResult();	

            $colors = $this->paginatorInterface->paginate($data, $page, 9);
            return $colors;
    }

//    /**
//     * @return Color[] Returns an array of Color objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Color
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
