<?php

namespace App\Repository;

use App\Entity\ModificationHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModificationHistory>
 *
 * @method ModificationHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModificationHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModificationHistory[]    findAll()
 * @method ModificationHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModificationHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModificationHistory::class);
    }

//    /**
//     * @return ModificationHistory[] Returns an array of ModificationHistory objects
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

//    public function findOneBySomeField($value): ?ModificationHistory
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}