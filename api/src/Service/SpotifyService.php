<?php

namespace App\Service;

use App\Events\SpotifyLogged;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SpotifyService
{
    /**
     * In the $_ENV var should be defined the following keys, write them in the .env.local file:
     * SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, SPOTIFY_REDIRECT_URI, SPOTIFY_REFRESH_TOKEN
     */

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $keys = ['SPOTIFY_CLIENT_ID', 'SPOTIFY_CLIENT_SECRET', 'SPOTIFY_REDIRECT_URI', 'SPOTIFY_REFRESH_TOKEN'];
        foreach($keys as $key) {
            if (!array_key_exists($key, $_ENV)) {
                throw new \InvalidArgumentException(sprintf('Undefined key "%s" in $_ENV, define it in .env file', $key));
            }
        }
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
    /*
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
    */
    public function getClient(): SpotifyWebAPI
    {
        $session = $this->getSpotifySession();
        if (!$session->refreshAccessToken($_ENV['SPOTIFY_REFRESH_TOKEN'])) {
            throw new \Exception('Invalid refresh token');
        }
        $client = new SpotifyWebAPI();
        $client->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
        $client->setAccessToken($session->getAccessToken());
        return $client;
    }

    public function refreshAccessToken(SpotifyWebAPI &$client, $refreshToken): bool {
        if (intval($client->getLastResponse()['status'],10) === 401) {
            $session = $this->getSpotifySession();
            if ($session->refreshAccessToken($refreshToken)) {
                $client->setAccessToken($session->getAccessToken());
                return true;
            }
        }
        return false;
    }

    private function getSpotifySession(): SpotifySession
    {
        return new SpotifySession(
            $_ENV['SPOTIFY_CLIENT_ID'],
            $_ENV['SPOTIFY_CLIENT_SECRET'],
            $_ENV['SPOTIFY_REDIRECT_URI']
        );
    }
}
