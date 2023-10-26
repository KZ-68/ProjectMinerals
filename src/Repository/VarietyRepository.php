<?php

namespace App\Repository;

use App\Entity\Variety;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Variety>
 *
 * @method Variety|null find($id, $lockMode = null, $lockVersion = null)
 * @method Variety|null findOneBy(array $criteria, array $orderBy = null)
 * @method Variety[]    findAll()
 * @method Variety[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VarietyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Variety::class);
    }

    public function findVarietiesCount() {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Variety[] Returns an array of Variety objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Variety
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
