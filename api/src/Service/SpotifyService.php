<?php

namespace App\Service;

use App\Events\SpotifyLogged;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SpotifyService
{
    const CLIENT_ID = '436110e3b7a548debaf2520ffc745888';
    const CLIENT_SECRET = '891fadc73eda4b08bb9daba41992cd11';
    const REDIRECT_URI = 'http://423067ff.ngrok.io/spotify/callback';

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Generate the Spotify authorization URL
     *
     * @return string
     */
    public function getAuthorizeUrl(): string
    {
        $session = $this->getSpotifySession();
        $options = [
            'scope' => [
                'playlist-modify-public',
                'playlist-modify-private',
            ],
        ];
        return $session->getAuthorizeUrl($options);
    }

    public function login(string $authorizationCode): SpotifySession
    {
        $session = $this->getSpotifySession();
        $session->requestAccessToken($authorizationCode);

        $event = new SpotifyLogged($session->getAccessToken(), $session->getRefreshToken());
        $this->dispatcher->dispatch($event, SpotifyLogged::NAME);

        return $session;
    }

    public function getClient($accessToken): SpotifyWebAPI
    {
        $client = new SpotifyWebAPI();
        $client->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
        $client->setAccessToken($accessToken);
        return $client;
    }

    public function refreshSession($refreshToken, SpotifyWebAPI $client): SpotifyWebAPI
    {
        $session = $this->getSpotifySession();
        $session->refreshAccessToken($refreshToken);
        $client->setAccessToken($session->getAccessToken());
        return $client;
    }

    private function getSpotifySession(): SpotifySession
    {
        return new SpotifySession(
            self::CLIENT_ID,
            self::CLIENT_SECRET,
            self::REDIRECT_URI
        );
    }
}
