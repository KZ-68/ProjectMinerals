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
            $dataQuery["name"] = $data
                ->orwhere('m.name LIKE :search')
                ->setParameter('search', $searchData['search'].'%')
                ->getQuery()
                ->getResult();
            if($dataQuery["name"] !== []) {
                return $dataQuery["name"];
            }
            if($dataQuery["name"] === []) {
                $dataQuery["formula"] = $data
                    ->orWhere('m.formula LIKE :search')
                    ->setParameter('search', $searchData['search'].'%')
                    ->getQuery()
                    ->getResult();
                if($dataQuery["formula"] !== []) {
                    return $dataQuery["formula"];
                }
            } 
            if ($dataQuery["formula"] === []) {
                $dataQuery["crystal_system"] = $data
                    ->orWhere('m.crystal_system LIKE :search')
                    ->setParameter('search', $searchData['search'].'%')
                    ->getQuery()
                    ->getResult();
                if($dataQuery["crystal_system"] !== []) {
                    return $dataQuery["crystal_system"];
                }
            } 
            if ($dataQuery["crystal_system"] === []) {
                $dataQuery["density"] = $data 
                    ->orWhere('m.density LIKE :search')
                    ->setParameter('search', $searchData['search'].'%')
                    ->getQuery()
                    ->getResult();
                if($dataQuery["density"] !== []) {
                    return $dataQuery["density"];
                }
            } 
            if ($dataQuery["density"] === []) {
                $dataQuery["hardness"] = $data
                    ->orWhere('m.hardness LIKE :search')
                    ->setParameter('search', $searchData['search'])
                    ->getQuery()
                    ->getResult();
                if($dataQuery["hardness"] !== []) {
                    return $dataQuery["hardness"];
                }
            } 
            if ($dataQuery["hardness"] === []) {
                $dataQuery["fracture"] = $data 
                    ->orWhere('m.fracture LIKE :search')
                    ->setParameter('search', $searchData['search'])
                    ->getQuery()
                    ->getResult();
                if($dataQuery["fracture"] !== []) {
                    return $dataQuery["fracture"];
                }
            } 
            if ($dataQuery["fracture"] === []) {
                $dataQuery["streak"] = $data 
                    ->orWhere('m.streak LIKE :search')
                    ->setParameter('search', $searchData['search'].'%')
                    ->getQuery()
                    ->getResult();
                if($dataQuery["streak"] !== []) {
                    return $dataQuery["streak"];
                }
            } 
            if ($dataQuery["streak"] === []) {
                $dataQuery["category"] = $data
                    ->innerJoin('m.category', 'c', 'WITH', 'c.id = m.category')
                    ->orWhere('c.name LIKE :search')
                    ->setParameter('search', $searchData['search'].'%')
                    ->getQuery()
                    ->getResult();
                if($dataQuery["category"] !== []) {
                    return $dataQuery["category"];
                }
            } 
            if ($dataQuery["category"] === []) {
                $dataQuery["varieties"] = $data 
                        ->leftJoin('m.varieties', 'v')
                        ->orWhere('v.name LIKE :search')
                        ->setParameter('search', $searchData['search'].'%')
                        ->getQuery()
                        ->getResult();
                if($dataQuery["varieties"] !== []) {
                    return $dataQuery["varieties"];
                }
            }
            if ($dataQuery["varieties"] === []) {
                $dataQuery["colors"] = $data 
                        ->leftJoin('m.colors', 'co')
                        ->orWhere('co.name LIKE :search')
                        ->setParameter('search', $searchData['search'].'%')
                        ->getQuery()
                        ->getResult();
                if($dataQuery["colors"] !== []) {
                    return $dataQuery["colors"];
                }
            }
            if ($dataQuery["colors"] === []) {
                $dataQuery["lustres"] = $data 
                        ->leftJoin('m.lustres', 'lu')
                        ->orWhere('lu.type LIKE :search')
                        ->setParameter('search', $searchData['search'].'%')
                        ->getQuery()
                        ->getResult();
                if($dataQuery["lustres"] !== []) {
                    return $dataQuery["lustres"];
                }
            }  
            if ($dataQuery["lustres"] == []) {
                $dataQuery = [];
                return $dataQuery;
            }
        } 

    }

    public function findBySearch(SearchData $searchData): PaginationInterface {
        $data = $this->createQueryBuilder('m');
        
        if(!empty($searchData->search)) {
            $data = $data
                ->orwhere('m.name LIKE :search')
                ->orWhere('m.formula LIKE :search')
                ->orWhere('m.crystal_system LIKE :search')
                ->orWhere('m.density LIKE :search')
                ->orWhere('m.hardness LIKE :search')
                ->orWhere('m.fracture LIKE :search')
                ->orWhere('m.streak LIKE :search')
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
                ->setParameter('name', $advancedSearchData['name'].'%');
            }
        
            if(!empty($advancedSearchData['formula'])) {
                $data = $data
                ->andWhere('m.formula LIKE :formula')
                ->setParameter('formula', $advancedSearchData['formula'].'%');
            }
            
            if(!empty($advancedSearchData['crystal_system'])) {
                $data = $data
                ->andWhere('m.crystal_system LIKE :crystal_system')
                ->setParameter('crystal_system', $advancedSearchData['crystal_system'].'%');
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
                ->setParameter('fracture', $advancedSearchData['fracture'].'%');
            }
            
            if(!empty($advancedSearchData['streak'])) {
                $data = $data
                ->andWhere('m.streak LIKE :streak')
                ->setParameter('streak', $advancedSearchData['streak'].'%');
            }

            if(!empty($advancedSearchData['category'])) {
                $data = $data
                ->innerJoin('m.category', 'c', 'WITH', 'c.id = m.category')
                ->andWhere('c.id = :category')
                ->setParameter('category', $advancedSearchData['category'].'%');
            }

            if(!empty($advancedSearchData['varieties'])) {
                $data = $data
                    ->leftJoin('m.varieties', 'v')
                    ->andWhere('v.id = :variety');
                foreach ($advancedSearchData['varieties'] as $variety) {
                    $data = $data
                    ->setParameter('variety', $variety.'%');
                }
            }
        
            if(!empty($advancedSearchData['colors'])) {
                $data = $data
                    ->leftJoin('m.colors', 'co')
                    ->andWhere('co.id = :color');
                foreach ($advancedSearchData['colors'] as $color) {
                    $data = $data
                    ->setParameter('color', $color.'%');
                }
            }
        
            if(!empty($advancedSearchData['lustres'])) {
                $data = $data    
                    ->leftJoin('m.lustres', 'lu')
                    ->andWhere('lu.id = :lustre');
                foreach ($advancedSearchData['lustres'] as $lustre) {
                    $data = $data
                    ->setParameter('lustre', $lustre.'%');
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

    public function findMineralsCount() {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteImageTitle($mineral) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();
        $query = $sub->update('App\Entity\Mineral','m')
            ->set('m.image_title', 'NULL')
            ->where('m.id = :id')
            ->setParameter('id', $mineral)
            ->getQuery();
        $query->execute();
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
