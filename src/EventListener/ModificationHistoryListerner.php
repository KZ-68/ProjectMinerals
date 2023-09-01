<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Mineral;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use App\Entity\ModificationHistory;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ModificationHistoryListener implements EventSubscriber
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getSubscribedEvents()
    {
        return [Events::preUpdate];
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $mineral = $args->getObject();
        $user = $tokenStorage->getToken()->getUser();
        
        if ($mineral instanceof Mineral) {
            $entityManager = $args->getObjectManager();
            $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($mineral);

            // Récupére les données d'origine de l'entité Mineral avant la modification.
            $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($mineral);

            // Compare les données d'origine avec les données actuelles pour enregistrer les modifications.
            $changes = [];
            foreach ($changeSet as $fieldName => $changes) {
                $oldValue = $originalData[$fieldName];
                $newValue = $changes[1];
                if ($oldValue !== $newValue) {
                    $changes[$fieldName] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            if (!empty($changes)) {
                // Créez une instance de MineralHistory et enregistrez les modifications.
                $modificationHistory = new ModificationHistory();
                $modificationHistory->setMineral($mineral);
                $modificationHistory->setUser($user);
                $modificationHistory->setChanges($changes);

                // Enregistrez l'historique des modifications.
                $entityManager->persist($modificationHistory);
                $entityManager->flush();
            }
        }
    }
}
