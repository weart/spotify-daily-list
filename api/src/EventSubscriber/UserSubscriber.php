<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Events\SpotifyLogged;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            SpotifyLogged::NAME => ['saveCredentials', EventPriorities::POST_WRITE],
        ];
    }

    public function saveCredentials(SpotifyLogged $event)
    {
        $this->entityManager->getRepository(User::class)->saveSpotifyCredentials(
            $event->getAccessToken(), $event->getRefreshToken()
        );
    }
}
