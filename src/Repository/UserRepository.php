<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry, 
        private PaginatorInterface $paginatorInterface
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function	findPaginateUsers(int $page): PaginationInterface {	
        
        $data = $this->createQueryBuilder('u')	
      	    ->orderBy('u.username', 'ASC')	
      	    ->getQuery()
            ->getResult();	

            $users = $this->paginatorInterface->paginate($data, $page, 9);
            return $users;
    }

    public function anonymizeUser(int $user, string $username) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();
        $query = $sub->update('App\Entity\User', 'u')
                    ->set('u.email', 'NULL')
                    ->set('u.username', ':username')
                    ->set('u.password', 'NULL')
                    ->set('u.avatar', 'NULL')
                    ->where('u.id = :id')
                    ->setParameter('id', $user)
                    ->setParameter('username', $username)
                    ->getQuery();
        $query->execute();
    }

    public function updateRoles(int $user, $roles) {
        // Connexion avec la base de données grâce à l'entity manager de l'instance    
        $conn = $this->getEntityManager()->getConnection();
        // Préparation de la requête UPDATE
        $sql = '
            UPDATE user u
            SET u.roles = :roles
            WHERE u.id = :id
            ';
        // Ajout des valeurs réelles et exécution.
        $conn->executeQuery($sql, ['roles' => json_encode([$roles]), 'id' => $user]);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
