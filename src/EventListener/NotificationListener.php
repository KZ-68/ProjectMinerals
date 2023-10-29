<?php

namespace App\EventListener;

use App\Entity\Comment;
use App\Entity\Discussion;
use App\Entity\Notification;
use App\Repository\CommentRepository;
use Doctrine\ORM\Event\PostPersistEventArgs;

class NotificationListener
{

    public function postPersist(PostPersistEventArgs $args)
    {
        $notification = new Notification();
        $comment = $args->getObject();

        if ($comment instanceof Comment) {
            $entityManager = $args->getObjectManager();
            $commentRepository = $entityManager->getRepository('App\Entity\Comment');

            if ($comment->getParent() === null) {
                $commentUser = $comment->getUser();
                $commentDiscussion = $comment->getDiscussion();

                $notification->setUser($commentUser);
                $notification->setComment($comment);
                $notification->setDiscussion($commentDiscussion);

                $commentUser->addNotification($notification);

                $entityManager->persist($notification);
                $entityManager->persist($commentUser);
                $entityManager->flush();
                
            }
            
            if ($comment->getParent() !== null) {
                $parent_comment_user = $comment->getParent()->getUser();
                $parent_id = $commentRepository->find($comment->getParent()->getId());
                $children = $parent_id->getChildren();
                
                foreach($children as $child) {
                    if ($child === $comment) {
                        $notification->setUser($parent_comment_user);
                        $notification->setComment($comment);
                    }
                }
                
                $parent_comment_user->addNotification($notification);
                
                $entityManager->persist($notification);
                $entityManager->persist($parent_comment_user);
                $entityManager->flush();
            }
            
        }
    }
}
