<?php

namespace App\Repository;

use App\Entity\Mineral;
use App\Model\SearchData;
use App\Model\AdvancedSearchData;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
      	    ->orderBy('m.name', 'ASC')	
      	    ->getQuery()
            ->getResult();	

            $minerals = $this->paginatorInterface->paginate($data, $page, 9);
            return $minerals;
    }

    public function findByAjaxSearch($searchData): array {
        $data = $this->createQueryBuilder('m');
        
        if(!empty($searchData['search'])) {
            $data = $data
                ->orwhere('m.name LIKE :search')
                ->orWhere('m.formula LIKE :search')
                ->orWhere('m.crystal_system LIKE :search')
                ->orWhere('m.density LIKE :search')
                ->orWhere('m.hardness LIKE :search')
                ->orWhere('m.fracture LIKE :search')
                ->orWhere('m.streak LIKE :search')
                ->innerJoin('m.category', 'c', 'WITH', 'c.id = m.category')
                ->join('m.varieties', 'v')
                ->join('m.colors', 'co')
                ->join('m.lustres', 'lu')
                ->orWhere('c.name LIKE :search')
                ->orWhere('v.name = :search')
                ->orWhere('co.name = :search')
                ->orWhere('lu.type = :search')
                ->setParameter('search', $searchData['search']);
        }
        
        $data = $data 
            ->getQuery()
            ->getResult();

        return $data;
    }

    public function findBySearch(SearchData $searchData): PaginationInterface {
        $data = $this->createQueryBuilder('m');
        
        if(!empty($searchData->search)) {
            $data = $data
                ->innerJoin('m.category', 'c', 'WITH', 'c.id = m.category')
                ->join('m.varieties', 'v')
                ->join('m.colors', 'co')
                ->join('m.lustres', 'lu')
                ->orwhere('m.name LIKE :search')
                ->orWhere('m.formula LIKE :search')
                ->orWhere('m.formula LIKE :search')
                ->orWhere('m.crystal_system LIKE :search')
                ->orWhere('m.density LIKE :search')
                ->orWhere('m.hardness LIKE :search')
                ->orWhere('m.fracture LIKE :search')
                ->orWhere('m.streak LIKE :search')
                ->orWhere('c.name LIKE :search')
                ->orWhere('v.name LIKE :search')
                ->orWhere('co.name LIKE :search')
                ->orWhere('lu.type LIKE :search')
                ->setParameter('search', $searchData->search);
        }
        
        $data = $data 
            ->getQuery()
            ->getResult();
        
        $minerals = $this->paginatorInterface->paginate($data, $searchData->page, 9);

        return $minerals;
    }

    public function findByAdvancedSearch($advancedSearchData): array {
        $data = $this
            // Select de l'objet Mineral avec l'alias m
            ->createQueryBuilder('m');
            // Si l'un des champs de données recherché n'est pas vide :
            if(!empty($advancedSearchData['name'])) {
                // On ajoute à la variable $data :
                $data = $data
                // Une chaine de caractère identique au champ existant
                ->andWhere('m.name LIKE :name')
                /* On met en paramètre le champ de la classe AdvancedSearchData,
                pour par la suite préparer la requête dans le controlleur */
                ->setParameter('name', $advancedSearchData['name']);
            }
        
            if(!empty($advancedSearchData['formula'])) {
                $data = $data
                ->andWhere('m.formula LIKE :formula')
                ->setParameter('formula', $advancedSearchData['formula']);
            }
            
            if(!empty($advancedSearchData['crystal_system'])) {
                $data = $data
                ->andWhere('m.crystal_system LIKE :crystal_system')
                ->setParameter('crystal_system', $advancedSearchData['crystal_system']);
            }

            if(!empty($advancedSearchData['density'])) {
                $data = $data
                ->andWhere('m.density LIKE :density')
                ->setParameter('density', $advancedSearchData['density']);
            }
            
            if(!empty($advancedSearchData['hardness'])) {
                $data = $data
                ->andWhere('m.hardness LIKE :hardness')
                ->setParameter('hardness', $advancedSearchData['hardness']);
            }
            
            if(!empty($advancedSearchData['fracture'])) {
                $data = $data
                ->andWhere('m.fracture LIKE :fracture')
                ->setParameter('fracture', $advancedSearchData['fracture']);
            }
            
            if(!empty($advancedSearchData['streak'])) {
                $data = $data
                ->andWhere('m.streak LIKE :streak')
                ->setParameter('streak', $advancedSearchData['streak']);
            }

            if(!empty($advancedSearchData['category'])) {
                $data = $data
                ->innerJoin('m.category', 'c', 'WITH', 'c.id = m.category')
                ->andWhere('c.id = :category')
                ->setParameter('category', $advancedSearchData['category']);
            }

            if(!empty($advancedSearchData['varieties'])) {
                $data = $data
                    ->leftJoin('m.varieties', 'v')
                    ->andWhere('v.id = :variety');
                foreach ($advancedSearchData['varieties'] as $variety) {
                    $data = $data
                    ->setParameter('variety', $variety);
                }
            }
        
            if(!empty($advancedSearchData['colors'])) {
                $data = $data
                    ->leftJoin('m.colors', 'co')
                    ->andWhere('co.id = :color');
                foreach ($advancedSearchData['colors'] as $color) {
                    $data = $data
                    ->setParameter('color', $color);
                }
            }
        
            if(!empty($advancedSearchData['lustres'])) {
                $data = $data    
                    ->leftJoin('m.lustres', 'lu')
                    ->andWhere('lu.id = :lustre');
                foreach ($advancedSearchData['lustres'] as $lustre) {
                    $data = $data
                    ->setParameter('lustre', $lustre);
                }
            }

        $data = $data 
            ->getQuery()
            ->getResult();

        return $data;
    }

    public function findMineralBySlug($slug) {
        return $this->createQueryBuilder('m')
           ->andWhere('m.slug = :slug')
           ->setParameter('slug', $slug)
           ->getQuery()
           ->getResult()
       ;
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
