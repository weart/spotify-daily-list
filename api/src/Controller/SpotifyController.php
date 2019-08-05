<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Poll;
use App\Service\SpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class SpotifyController extends AbstractController
{
    /**
     * @Route(
     *     name="spotify_login",
     *     path="/spotify/login",
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::login"
     *     }
     *   )
     */
    public function login(SpotifyService $spotifyService)
    {
//        var_dump($spotifyService->getAuthorizeUrl());exit;
        return $this->redirect($spotifyService->getAuthorizeUrl());
    }

    /**
     * @Route(
     *     name="spotify_callback",
     *     path="/spotify/callback",
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::callback"
     *     }
     *   )
     */
    public function callback(Request $request, SpotifyService $spotifyService)
    {
        if (!$request->query->has('code'))
        {
            throw new \Exception('Undefined code');
        }
        $spotifySession = $spotifyService->login($request->query->get('code'));

        return $this->json([
            'ok',
            'code'=> $request->query->get('code'),
            'access_token' => $spotifySession->getAccessToken(),
            'refresh_token' => $spotifySession->getRefreshToken()
        ]);
    }

    /**
     * @Route(
     *     name="spotify_track_info",
     *     path="/spotify/track_info/{uri}",
     *     requirements={"uri"="([a-zA-Z0-9]+)(.*)"},
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::getTrackInfo"
     *     }
     *   )
     */
    public function getTrackInfo($uri, SpotifyService $spotifyService, EntityManagerInterface $entityManager)
    {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);

        $trackInfo = $client->getTrack($uri);
        return $this->json([
            'hydra:member' => [
                'ok',
                'track_id' => $uri,
                'artist_name' => current($trackInfo['artists'])['name'],
                'track_name' => $trackInfo['name'],
                'image' => next($trackInfo['album']['images'])['url'], //second image
                'all_info' => $trackInfo
            ]
        ]);
    }

    /**
     * @Route(
     *     name="spotify_playlist_create",
     *     path="/spotify/playlist/{name}",
     *     methods={"PUT"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::createPlaylist"
     *     }
     *   )
     */
    public function createPlaylist(string $name, SpotifyService $spotifyService, EntityManagerInterface $entityManager)
    {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);

        $playlist = $client->createPlaylist(['name' => $name, 'public' => true]);
        if (!$playlist)
        {
            throw new \Exception(sprintf('Error creating the playlist: %s',$name));
        }
        return $this->json($playlist); //@ToDo?
    }

    /**
     * @Route(
     *     name="spotify_add_track_to_playlist",
     *     path="/spotify/playlist/{playlist}/{track}",
     *     methods={"PUT"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::addTrackToPlaylist"
     *     }
     *   )
     */
    public function addTrackToPlaylist(
        string $playlist, string $track,
        SpotifyService $spotifyService, EntityManagerInterface $entityManager
    ) {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);

        if (!$client->addPlaylistTracks($playlist, $track))
        {
            throw new \Exception(sprintf('Error adding song %s to playlist: %s', $playlist, $track));
        }
        return $this->json($playlist); //@ToDo?
    }

    /**
     * @Route(
     *     name="spotify_get_playlist_tracks",
     *     path="/spotify/playlist/{playlist}",
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::getPlaylistTracks"
     *     }
     *   )
     */
    public function getPlaylistTracks(string $playlist, SpotifyService $spotifyService, EntityManagerInterface $entityManager)
    {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);
        return $this->json(
            $client->getPlaylist($playlist)
        );
    }

    /**
     * This methods should be in a unique controller like commented FinishPollController
     */

    /**
     * Route(
     *     name="poll_finish",
     *     path="/polls/{id}/finish",
     *     requirements={"uuid"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},
     *     methods={"PUT"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::finishPoll",
     *     }
     * )
     */
    public function finishPoll($id, SpotifyService $spotifyService, EntityManagerInterface $entityManager): Poll
    {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);

        /**
         * @var $poll Poll
         */
        $poll = $entityManager->find(Poll::class, $id);
        if (!$poll) {
            throw new \Exception('Invalid poll id');
        }

        if ($poll->restartPoll) {
            if (empty($poll->spotifyWinnerPlaylistUri) || empty($poll->spotifyHistoricPlaylistUri)) {
                throw new \Exception('Poll without winner & historical playlist');
            }
            $tracks = $poll->getTrackOrderByVoted();
            if (!$client->addPlaylistTracks($poll->spotifyWinnerPlaylistUri, current($tracks)['spotify_uri']))
            {
                throw new \Exception(sprintf(
                    'Error adding the track winner %s into the playlist: %s',
                    current($tracks)['spotify_uri'], $poll->spotifyWinnerPlaylistUri
                ));
            }
            if (!$client->addPlaylistTracks($poll->spotifyHistoricPlaylistUri, array_column($tracks, 'spotify_uri')))
            {
                throw new \Exception(sprintf(
                    'Error adding %s historical tracks into the playlist: %s',
                    count($tracks), $poll->spotifyHistoricPlaylistUri
                ));
            }
            if (!$client->deletePlaylistTracks($poll->spotifyPlaylistUri))
            {
                throw new \Exception(sprintf('Error while truncate the playlist: %s',$poll->spotifyPlaylistUri));
            }

        } else {
            $poll->markAsEnded();
            $entityManager->persist($poll);
            $entityManager->flush();
        }
        return $this->json($poll->toArray());
    }

    /**
     * Route(
     *     name="poll_finish",
     *     path="/polls/{id}/restart",
     *     requirements={"uuid"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},
     *     methods={"PUT"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::restartPoll",
     *     }
     * )
     */
    public function restartPoll($id, SpotifyService $spotifyService, EntityManagerInterface $entityManager): Poll
    {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);

        /**
         * @var $poll Poll
         */
        $poll = $entityManager->find(Poll::class, $id);
        if (!$poll) {
            throw new \Exception('Invalid poll id');
        }

        if ($poll->restartPoll) {
            throw new \Exception('Poll not setted as restartable');
        }
        if (empty($poll->spotifyWinnerPlaylistUri) || empty($poll->spotifyHistoricPlaylistUri)) {
            throw new \Exception('Poll without winner & historical playlist');
        }
        $tracks = $poll->getTrackOrderByVoted();
        if (!$client->addPlaylistTracks($poll->spotifyWinnerPlaylistUri, current($tracks)['spotify_uri']))
        {
            throw new \Exception(sprintf(
                'Error adding the track winner %s into the playlist: %s',
                current($tracks)['spotify_uri'], $poll->spotifyWinnerPlaylistUri
            ));
        }
        if (!$client->addPlaylistTracks($poll->spotifyHistoricPlaylistUri, array_column($tracks, 'spotify_uri')))
        {
            throw new \Exception(sprintf(
                'Error adding %s historical tracks into the playlist: %s',
                count($tracks), $poll->spotifyHistoricPlaylistUri
            ));
        }

        if (!$client->deletePlaylistTracks($poll->spotifyPlaylistUri))
        {
            throw new \Exception(sprintf('Error while truncate the playlist: %s',$poll->spotifyPlaylistUri));
        }

        return $this->json($poll->toArray());
    }
}
