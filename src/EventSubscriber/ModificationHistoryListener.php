<?php

namespace App\EventSubscriber;

use App\Entity\Color;
use App\Entity\Lustre;
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
        $lustre = $args->getObject();
       
        $user = $this->tokenStorage->getToken()->getUser();
        
        if ($mineral instanceof Mineral) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($mineral);
            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($color);

            $colorsCollection = $mineral->getColors();
            $lustresCollection = $mineral->getLustres();

            $insertedColors = $colorsCollection->getInsertDiff();
            $deletedColors = $colorsCollection->getDeleteDiff();

            $insertedLustres = $lustresCollection->getInsertDiff();
            $deletedLustres = $lustresCollection->getDeleteDiff();

            $modificationHistory = new ModificationHistory();
            $modificationHistory->setMineral($mineral);
            $modificationHistory->setUser($user);

            if ($insertedColors && $deletedColors) {
                $modifiedDataColors = [
                    'color' => [
                    $insertedColors[0]->getName(),
                    $deletedColors[0]->getName(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataColors, $changeSet]);
            } else if ($insertedLustres && $deletedLustres) {
                $modifiedDataLustres = [
                    'lustre' => [
                    $insertedLustres[0]->getType(),
                    $deletedLustres[0]->getType(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataLustres, $changeSet]);
            } else if ($insertedColors && $deletedColors && $insertedLustres && $deletedLustres) {
                $modifiedDataColors = [
                    'color' => [
                    $insertedColors[0]->getName(),
                    $deletedColors[0]->getName(),
                    ]
                ];
                $modifiedDataLustres = [
                    'lustre' => [
                    $insertedLustres[0]->getType(),
                    $deletedLustres[0]->getType(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataColors, $modifiedDataLustres, $changeSet]);
            }else {
                $modificationHistory->setChanges([$changeSet]);
            } 
            $mineral->addModificationHistory($modificationHistory);
            $user->addModificationHistory($modificationHistory);
            // On enregistre l'historique des modifications.
            $entityManager->persist($modificationHistory);
            $entityManager->persist($mineral);
            $entityManager->flush();
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

        if($lustre instanceof Lustre) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($lustre);

            if ($changeSet != []) {
                $modificationHistory = new ModificationHistory();
                $modificationHistory->setLustre($lustre);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges($changeSet);
                $lustre->addModificationHistory($modificationHistory);
                $user->addModificationHistory($modificationHistory);

                $entityManager->persist($modificationHistory);
                $entityManager->persist($lustre);
                $entityManager->flush();
            } 
        }
    }
}
