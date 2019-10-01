<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Events\SpotifyLogged;
//use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            SpotifyLogged::NAME => ['newCredentials', EventPriorities::POST_WRITE],
        ];
    }

    public function newCredentials(SpotifyLogged $event)
    {
        $this->logger->info('newCredentials', [
            'accessToken' => $event->getAccessToken(),
            'refreshToken' => $event->getRefreshToken()
        ]);
//        $this->entityManager->getRepository(User::class)->saveSpotifyCredentials(
//            $event->getAccessToken(), $event->getRefreshToken()
//        );
    }
}
