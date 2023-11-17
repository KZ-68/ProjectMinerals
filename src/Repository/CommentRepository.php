<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function deleteCommentByModerator(int $comment) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();
        $query = $sub->update('App\Entity\Comment', 'c')
                    ->set('c.isDeletedByModerator', 1)
                    ->where('c.id = :id')
                    ->setParameter('id', $comment)
                    ->getQuery();
        $query->execute();
    }

    public function deleteCommentByUser(int $comment) {
        // On récupère l'entity manager de l'instance Comment actuelle
        $em = $this->getEntityManager();
        // On utilise le queryBuilder
        $sub = $em->createQueryBuilder();
        // On met à jour cette instance et on met un alias
        $query = $sub->update('App\Entity\Comment', 'c')
                    // On met le booléen sur True
                    ->set('c.isDeletedByUser', 1)
                    // On applique un marqueur nommé
                    ->where('c.id = :id')
                    // On remplace par la valeur réelle
                    ->setParameter('id', $comment)
                    // On récupère la requête
                    ->getQuery();
        // On exécute la requête            
        $query->execute();
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
