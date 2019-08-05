<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Poll;
use App\Entity\User;
use App\Events\SpotifyLogged;
use App\Service\SpotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SpotifySubscriber implements EventSubscriberInterface
{
    private $spotify;
    private $em;
    private $logger;

    public function __construct(SpotifyService $spotify, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->spotify = $spotify;
        $this->em = $em;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            SpotifyLogged::NAME => ['logCredentials', EventPriorities::PRE_RESPOND],
            KernelEvents::VIEW => ['testWrite', EventPriorities::POST_WRITE]
        ];
    }

    public function logCredentials(SpotifyLogged $event)
    {
        $this->logger->info('SpotifyLogged', [
            'event' => $event
        ]);
    }

    public function testWrite(ViewEvent $event)
    {
        $poll = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$poll instanceof Poll || Request::METHOD_POST !== $method) {
            return;
        }

        if (!empty($poll->spotifyPlaylistUri)) {
            return;
        }

        $this->logger->info('testWrite', [
            'event' => $event,
            'res' => get_class($poll)
        ]);

        $playlist = $this->getClient()->createPlaylist(['name' => $poll->name, 'public' => true]);
        if (!$playlist)
        {
            throw new \Exception(sprintf('Error creating the playlist: %s',$poll->name));
        }
        $this->logger->info('playlist', [
            'item' => $playlist,
        ]);

        $poll->spotifyPlaylistUri = $playlist['id'];
        $this->em->persist($poll);
        $this->em->flush();
    }

    private function getClient()
    {
        list($accessToken, $refreshToken) = $this->em->getRepository(User::class)->getSpotifyCredentials();
        return $this->spotify->getClient($accessToken);
    }
}
