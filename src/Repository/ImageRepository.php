<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry, 
        private PaginatorInterface $paginatorInterface 
        )
    {
        parent::__construct($registry, Image::class);
    }

    public function findImagesAndNameInMineral($mineral): array {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder(); 
        $sub->select('i') 
            ->from('App\Entity\Image', 'i') 
            ->leftJoin('i.variety', 'v')
            ->leftJoin('v.mineral', 'm')
            ->where('m.id = :id')
            ->setParameter(':id', $mineral);
            return $sub->getQuery()->getResult();

    }

    public function findImagesById($mineral): array {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder(); 
        $sub->select('i') 
            ->from('App\Entity\Image', 'i') 
            ->innerJoin('i.mineral', 'm', 'WITH', 'm.id = i.mineral')
            ->where('i.variety IS NULL')
            ->andWhere('m.id = :id')
            ->setMaxResults(1)
            ->setParameter(':id', $mineral);
        return $sub->getQuery()->getResult();

    }

    public function	findPaginateImages(int $page): PaginationInterface {	
        
        $data = $this->createQueryBuilder('i')	
      	    ->orderBy('i.filename', 'ASC')	
      	    ->getQuery()
            ->getResult();	

            $images = $this->paginatorInterface->paginate($data, $page, 12);
            return $images;
    }

//    /**
//     * @return Image[] Returns an array of Image objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Image
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
