<?php

namespace App\Repository;

use App\Entity\Lustre;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface
    )
    {
        parent::__construct($registry, Lustre::class);
    }

    public function	findPaginateLustres(int $page): PaginationInterface {	
        
        $data = $this->createQueryBuilder('l')	
      	    ->orderBy('l.type', 'ASC')	
      	    ->getQuery()
            ->getResult();	

            $lustres = $this->paginatorInterface->paginate($data, $page, 9);
            return $lustres;
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
