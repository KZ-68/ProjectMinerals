<?php

namespace App\EventSubscriber;

use App\Entity\Color;
use App\Entity\Mineral;
use Doctrine\ORM\Events;
use App\Entity\ModificationHistory;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ModificationHistoryListener implements EventSubscriber
{

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::postUpdate];
    }

    public function postUpdate(PostUpdateEventArgs $args)
    {
        $mineral = $args->getObject();
        $color = $args->getObject();
       
        $user = $this->tokenStorage->getToken()->getUser();
        
        if ($mineral instanceof Mineral) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($mineral);
            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($color);

            $colorsCollection = $mineral->getColors();

            $inserted = $colorsCollection->getInsertDiff();
            $deleted = $colorsCollection->getDeleteDiff();

            if ($inserted && $deleted) {
                $modifiedDataColors = [
                    'color' => [
                    $inserted[0]->getName(),
                    $deleted[0]->getName(),
                    ]
                ];
                    // On crée une instance de MineralHistory et on enregistre les modifications.
                $modificationHistory = new ModificationHistory();
                $modificationHistory->setMineral($mineral);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges([$modifiedDataColors, $changeSet]);
                $mineral->addModificationHistory($modificationHistory);
                $user->addModificationHistory($modificationHistory);
                // On enregistre l'historique des modifications.
                $entityManager->persist($modificationHistory);
                $entityManager->persist($mineral);
                $entityManager->flush();
            } else {
                $modificationHistory = new ModificationHistory();
                $modificationHistory->setMineral($mineral);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges([$changeSet]);
                $mineral->addModificationHistory($modificationHistory);
                $user->addModificationHistory($modificationHistory);
                // On enregistre l'historique des modifications.
                $entityManager->persist($modificationHistory);
                $entityManager->persist($mineral);
                $entityManager->flush();
            } 
        }

        if($color instanceof Color) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($color);

            if ($changeSet != []) {
                $modificationHistory = new ModificationHistory();
                $modificationHistory->setColor($color);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges($changeSet);
                $color->addModificationHistory($modificationHistory);
                $user->addModificationHistory($modificationHistory);

                $entityManager->persist($modificationHistory);
                $entityManager->persist($color);
                $entityManager->flush();
            } 
        }
    }
}
