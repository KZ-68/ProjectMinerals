<?php

namespace App\Repository;

use App\Entity\Lustre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lustre>
 *
 * @method Lustre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lustre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lustre[]    findAll()
 * @method Lustre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LustreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lustre::class);
    }

//    /**
//     * @return Lustre[] Returns an array of Lustre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lustre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
