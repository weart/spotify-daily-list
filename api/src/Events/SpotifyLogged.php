<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class SpotifyLogged extends Event
{
    public const NAME = 'spotify.login';

    protected $accessToken;
    protected $refreshToken;

    public function __construct(string $accessToken, string $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
