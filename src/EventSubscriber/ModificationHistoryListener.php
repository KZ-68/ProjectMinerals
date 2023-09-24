<?php

namespace App\EventSubscriber;

use App\Entity\Color;
use App\Entity\Image;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Variety;
use Doctrine\ORM\Events;
use App\Entity\Coordinate;
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
        $image = $args->getObject();
        $variety = $args->getObject();
       
        $user = $this->tokenStorage->getToken()->getUser();
        
        if ($mineral instanceof Mineral) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($mineral);

            $colorsCollection = $mineral->getColors();
            $lustresCollection = $mineral->getLustres();
            $imagesCollection = $mineral->getImages();
            $coordinatesCollection = $mineral->getCoordinates();
            $varietiesCollection = $mineral->getVarieties();

            $insertedColors = $colorsCollection->getInsertDiff();
            $deletedColors = $colorsCollection->getDeleteDiff();

            $insertedLustres = $lustresCollection->getInsertDiff();
            $deletedLustres = $lustresCollection->getDeleteDiff();

            $insertedImages = $imagesCollection->getInsertDiff();
            $deletedImages = $imagesCollection->getDeleteDiff();

            $insertedCoordinates = $coordinatesCollection->getInsertDiff();
            $deletedCoordinates = $coordinatesCollection->getDeleteDiff();

            $insertedVarieties = $varietiesCollection->getInsertDiff();
            $deletedVarieties = $varietiesCollection->getDeleteDiff();

            $modificationHistory = new ModificationHistory();
            $modificationHistory->setMineral($mineral);
            $modificationHistory->setUser($user);

            
            if ($deletedColors && !$insertedColors) {
                $modifiedDataColors = [
                    'color' => [
                    $deletedColors[0]->getName()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataColors, $changeSet]);
            } else if ($deletedLustres && !$insertedLustres) {
                $modifiedDataLustres = [
                    'lustre' => [
                    $deletedLustres[0]->getType()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataLustres, $changeSet]);
            } else if ($deletedImages && !$insertedImages) {
                $modifiedDataImages = [
                    'image' => [
                    $deletedImages[0]->getFilename()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataImages, $changeSet]);
            } else if ($deletedCoordinates && !$insertedCoordinates) {
                $modifiedDataCoordinates = [
                    'coordinate' => [
                    $deletedCoordinates[0]->getLatitude()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataCoordinates, $changeSet]);
            } else if ($deletedVarieties && !$insertedVarieties) {
                $modifiedDataVarieties = [
                    'variety' => [
                    $deletedVarieties[0]->getName()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataVarieties, $changeSet]);
            } else if ($insertedColors && !$deletedColors) {
                $modifiedDataColors = [
                    'color' => [
                    $insertedColors[0]->getName()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataColors, $changeSet]);
            } else if ($insertedLustres && !$deletedLustres) {
                $modifiedDataLustres = [
                    'lustre' => [
                    $insertedLustres[0]->getType()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataLustres, $changeSet]);
            } else if ($insertedImages && !$deletedImages) {
                $modifiedDataImages = [
                    'image' => [
                    $insertedImages[0]->getFilename()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataImages, $changeSet]);
            } else if ($insertedCoordinates && !$deletedCoordinates) {
                $modifiedDataCoordinates = [
                    'coordinate' => [
                    $insertedCoordinates[0]->getLatitude(),
                    $insertedCoordinates[0]->getLongitude()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataCoordinates, $changeSet]);
            } else if ($insertedVarieties && !$deletedVarieties) {
                $modifiedDataVarieties = [
                    'variety' => [
                    $insertedVarieties[0]->getName(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataVarieties, $changeSet]);
            } else if($insertedImages && $insertedCoordinates && !$deletedImages && !$deletedCoordinates) {
                $modifiedDataImages = [
                    'image' => [
                    $insertedImages[0]->getFilename()
                    ]
                ];
                $modifiedDataCoordinates = [
                    'coordinate' => [
                    $insertedCoordinates[0]->getLatitude(),
                    $insertedCoordinates[0]->getLongitude()
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataImages, $modifiedDataCoordinates, $changeSet]);
            } else if ($insertedColors && $deletedColors) {
                $modifiedDataColors = [
                    'color' => [
                    $deletedColors[0]->getName(),   
                    $insertedColors[0]->getName(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataColors, $changeSet]);
            } else if ($insertedLustres && $deletedLustres) {
                $modifiedDataLustres = [
                    'lustre' => [
                    $deletedLustres[0]->getType(),
                    $insertedLustres[0]->getType(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataLustres, $changeSet]);
            } else if (
                $insertedColors && $deletedColors && 
                $insertedLustres && $deletedLustres
                ) {
                $modifiedDataColors = [
                    'color' => [
                    $deletedColors[0]->getName(),
                    $insertedColors[0]->getName(),
                    ]
                ];
                $modifiedDataLustres = [
                    'lustre' => [
                    $deletedLustres[0]->getType(),
                    $insertedLustres[0]->getType(),
                    ]
                ];
                $modificationHistory->setChanges([$modifiedDataColors, $modifiedDataLustres, $changeSet]);
            } else if (
                $insertedColors && $deletedColors && 
                $insertedCoordinates) {
                $modifiedDataColors = [
                    'color' => [
                    $deletedColors[0]->getName(),
                    $insertedColors[0]->getName(),
                    ]
                ];
                $modifiedDataCoordinates = [
                    'coordinate' => [
                    $insertedCoordinates[0]->getLatitude(),
                    $insertedCoordinates[1]->getLongitude(),
                    ]
                ];
                $modificationHistory->setChanges([
                    $modifiedDataColors, 
                    $modifiedDataCoordinates,
                    $changeSet
                ]);
            } else if (
                $insertedColors && $deletedColors && 
                $insertedLustres && $deletedLustres && 
                $insertedImages) {
                $modifiedDataColors = [
                    'color' => [
                    $deletedColors[0]->getName(),
                    $insertedColors[0]->getName(),
                    ]
                ];
                $modifiedDataLustres = [
                    'lustre' => [
                    $deletedLustres[0]->getType(),
                    $insertedLustres[0]->getType(),
                    ]
                ];
                $modifiedDataImages = [
                    'image' => [
                    $insertedImages[0]->getFilename(),
                    ]
                ];
                $modificationHistory->setChanges([
                    $modifiedDataColors, 
                    $modifiedDataLustres, 
                    $modifiedDataImages,
                    $changeSet
                ]);
            } else if (
                $insertedColors && $deletedColors && 
                $insertedLustres && $deletedLustres && 
                $insertedImages &&
                $insertedCoordinates) {
                $modifiedDataColors = [
                    'color' => [
                    $deletedColors[0]->getName(),
                    $insertedColors[0]->getName(),
                    ]
                ];
                $modifiedDataLustres = [
                    'lustre' => [
                    $deletedLustres[0]->getType(),
                    $insertedLustres[0]->getType(),
                    ]
                ];
                $modifiedDataImages = [
                    'image' => [
                    $insertedImages[0]->getFilename(),
                    ]
                ];
                $modifiedDataCoordinates = [
                    'coordinate' => [
                    $insertedCoordinates[0]->getLatitude(),
                    $insertedCoordinates[0]->getLongitude()
                    ]
                ];
                $modificationHistory->setChanges([
                    $modifiedDataColors, 
                    $modifiedDataLustres, 
                    $modifiedDataImages,
                    $modifiedDataCoordinates,
                    $changeSet
                ]);
            } else {
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

        if($image instanceof Image) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($image);

            if ($changeSet != []) {
                $modificationHistory = new ModificationHistory();
                $modificationHistory->setImage($image);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges($changeSet);
                $image->addModificationHistory($modificationHistory);
                $user->addModificationHistory($modificationHistory);

                $entityManager->persist($modificationHistory);
                $entityManager->persist($image);
                $entityManager->flush();
            } 
        }

        if($variety instanceof Variety) {
            $entityManager = $args->getObjectManager();

            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($variety);

            $insertedImages = $imagesCollection->getInsertDiff();
            $deletedImages = $imagesCollection->getDeleteDiff();

            if ($changeSet != [] && $insertedImages) {
                $modificationHistory = new ModificationHistory();
                $modifiedDataImages = [
                    'image' => [
                    $insertedImages[0]->getFilename(),
                    ]
                ];
                $modificationHistory->setVariety($variety);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges($modifiedDataImages, $changeSet);
                $variety->addModificationHistory($modificationHistory);
                $user->addModificationHistory($modificationHistory);

                $entityManager->persist($modificationHistory);
                $entityManager->persist($variety);
                $entityManager->flush();
            } 
        }
    }
}
