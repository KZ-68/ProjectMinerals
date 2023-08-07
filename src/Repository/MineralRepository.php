<?php

namespace App\Repository;

use App\Entity\Mineral;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;

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
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface 
        )
    {
        parent::__construct($registry, Mineral::class);
    }

    public function	findPaginateMinerals(int $page): PaginationInterface {	
        
        $data = $this->createQueryBuilder('m')	
      	    ->orderBy('m.name', 'DESC')	
      	    ->getQuery()
            ->getResult();	

            $minerals = $this->paginatorInterface->paginate($data, $page, 9);
            return $minerals;
    }

    public function findBySearch(SearchData $searchData): PaginationInterface {
        $data = $this->createQueryBuilder('m');
        
        if(!empty($searchData->q)) {
            $data = $data  
                ->andWhere('m.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }
        $data = $data 
            ->getQuery()
            ->getResult();
        
        $minerals = $this->paginatorInterface->paginate($data, $searchData->page, 9);

        return $minerals;
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
