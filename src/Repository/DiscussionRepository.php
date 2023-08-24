<?php

namespace App\Repository;

use App\Entity\Discussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discussion>
 *
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    public function removeDiscussion(int $discussion) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();
        $query = $sub->update('App\Entity\Discussion', 'd')
                    ->set('d.content', 'NULL')
                    ->where('d.id = :id')
                    ->setParameter('id', $discussion)
                    ->getQuery();
        $query->execute();
    }

    public function moveDiscussionDeleted(int $discussion, string $discussionDeleted) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();
        $query = $sub->update('App\Entity\Discussion', 'd')
                    ->set('d.discussionDeleted', ':discussionDeleted')
                    ->where('d.id = :id')
                    ->setParameter('discussionDeleted', $discussionDeleted)
                    ->setParameter('id', $discussion)
                    ->getQuery();
        $query->execute();
    }

//    /**
//     * @return Discussion[] Returns an array of Discussion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Discussion
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
