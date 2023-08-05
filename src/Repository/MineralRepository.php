<?php

namespace App\Repository;

use App\Entity\Mineral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mineral>
 *
 * @method Mineral|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mineral|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mineral[]    findAll()
 * @method Mineral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MineralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mineral::class);
    }

    public function	getPaginationQuery() {	
        return $this->createQueryBuilder('m')	
      	    ->orderBy('m.name', 'DESC')	
      	    ->getQuery();	
    }

//    /**
//     * @return Mineral[] Returns an array of Mineral objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mineral
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
