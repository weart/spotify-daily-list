<?php

namespace App\Controller;

use App\Entity\User;
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
     *     path="/api/spotify_login",
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::spotify_login"
     *     }
     *   )
     */
    public function spotify_login(SpotifyService $spotifyService)
    {
//        var_dump($spotifyService->getAuthorizeUrl());exit;
        return $this->redirect($spotifyService->getAuthorizeUrl());
    }

    /**
     * @Route(
     *     name="spotify_callback",
     *     path="/api/spotify_callback",
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
     *     path="/api/spotify_track_info",
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::getTrackInfo"
     *     }
     *   )
     */
    public function getTrackInfo(Request $request, SpotifyService $spotifyService, EntityManagerInterface $entityManager)
    {
        if (!$request->query->has('id'))
        {
            throw new \Exception('Undefined id');
        }

        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
        $client = $spotifyService->getClient($accessToken);

        $trackInfo = $client->getTrack($request->query->get('id'));
        return $this->json([
            'ok',
            'track_id' => $request->query->get('id'),
            'artist_name' => current($trackInfo['artists'])['name'],
            'track_name' => $trackInfo['name'],
            'image' => next($trackInfo['album']['images'])['url'], //second image
            'all_info' => $trackInfo
        ]);
    }

    /**
     * @Route(
     *     name="test",
     *     path="/api/test",
     *     methods={"GET"},
     *     defaults={
     *       "_controller"="\App\Controller\SpotifyController::test"
     *     }
     *   )
     */
    public function test(Request $request, SpotifyService $spotifyService, EntityManagerInterface $entityManager)
    {
        list($accessToken, $refreshToken) = $entityManager->getRepository(User::class)->getSpotifyCredentials();
//        $accessToken = $request->query->get('access');
//        $refreshToken = $request->query->get('refresh');

        $client = $spotifyService->getClient($accessToken);

        $me = $client->me();
        var_dump($me);exit;
//        $re1 = $client->getLastResponse();
//        $track_id = $request->query->get('id');
//        $track = $client->getTrack($track_id);
//        $re2 = $client->getLastResponse();
//        $playlists = $client->getMyPlaylists();
//        $re3 = $client->getLastResponse();
//
//        var_dump($playlists);exit;
//
//        return $this->json([
//            'ok' => true,
//            'me'=> $me,
//            'track_id'=> $track_id,
//            'track'=> $track,
//            'playlist'=>$playlists,
//            'response1' => $re1,
//            'response2' => $re2,
//            'response3' => $re3
//        ]);

        //Print playlist
//        $playlists = $client->getMyPlaylists(['limit' => 50]);
//        foreach ($playlists->items as $playlist) {
//            echo '<a href="' . $playlist->external_urls->spotify . '">' . $playlist->name . '</a> <br>';
//        }

        //Create a playlist
//        $client->createPlaylist([
//            'name' => 'My shiny playlist'
//        ]);
        //Update playlist
//        $client->updatePlaylist('PLAYLIST_ID', [
//            'name' => 'New name'
//        ]);
        //Add tracks to playlist
//        $client->addPlaylistTracks('PLAYLIST_ID', [
//            'TRACK_ID',
//            'TRACK_ID'
//        ]);

//        exit;
    }
}
