<?php

namespace App\EventSubscriber;

use App\Events\SpotifyLogged;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\SpotifyService;
use ApiPlatform\Core\EventListener\EventPriorities;
use Psr\Log\LoggerInterface;

final class SpotifySubscriber implements EventSubscriberInterface
{
    private $spotify;
    private $logger;

    public function __construct(SpotifyService $spotify, LoggerInterface $logger)
    {
        $this->spotify = $spotify;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            SpotifyLogged::NAME => ['logCredentials', EventPriorities::PRE_RESPOND],
        ];
    }

    public function logCredentials(SpotifyLogged $event)
    {
        $this->logger->info('SpotifyLogged', [
            'event' => $event
        ]);
    }
}
