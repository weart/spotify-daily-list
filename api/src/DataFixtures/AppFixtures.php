<?php

namespace App\DataFixtures;

use App\Entity\Membership;
use App\Entity\Organization;
use App\Entity\Poll;
use App\Entity\Session;
use App\Entity\Track;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getGroups(): array
    {
        return ['polls'];
    }

    public function load(ObjectManager $manager)
    {
        $organization = new Organization('Discoveryfy');
        $manager->persist($organization);
        $user = new User('Leninux', 'discoverify@fabri.cat');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'1234567890'));
        $user_session = new Session($user->getUsername());
        $manager->persist($user_session);
        $user->addSession($user_session);
//        $user_session->setUser($user);
        $manager->persist($user);
        $membership = new Membership($user, $organization, Membership::OWNER);
        $manager->persist($membership);

        $sessions = [];
        for ($i = 0; $i < 10; $i++) {
            $session = new Session('Voter '.$i);
            $manager->persist($session);
            $sessions[$i] = $session;
        }
        unset($session);
        $manager->flush();

        $poll_properties = [
            'SpotifyPlaylistPublic' => false,
            'SpotifyPlaylistCollaborative' => false,
            'PublicVisibility' => true,
        ];
        $poll_properties_public = [
            'SpotifyPlaylistCollaborative' => true,
            'PublicVotes' => true,
            'AnonCanVote' => true,
            'WhoCanAddTrack' => 4, //Everyone
            'AnonVotesMaxRating' => 5,
            'UserVotesMaxRating' => 10,
            'MultipleAnonTracks' => true,
            'MultipleUserTracks' => true,
        ];
        $poll_properties_members = [
            'PublicVotes' => false,
            'AnonCanVote' => false,
            'WhoCanAddTrack' => 2, //Members
            'AnonVotesMaxRating' => 0,
            'UserVotesMaxRating' => 10,
            'MultipleAnonTracks' => false,
            'MultipleUserTracks' => true,
        ];
        $poll_properties_no_editable = [
            'SpotifyPlaylistCollaborative' => false,
            'WhoCanAddTrack' => null //nobody
        ];
        $poll_properties_weekly = [
            'RestartDate' => '0 0 * * 1' //"At 00:00 on Monday"
        ];
        $poll_properties_monthly = [
            'RestartDate' => '0 0 1 * *' //"At 00:00 on day-of-month 1"
        ];
//        $poll_properties_finished = [
//            'EndDate' => setEndDate is not a function (is markAsEnded)
//        ];

        $polls_schema = [
            'poll_public_weekly' => array_merge([
                    'Name' => 'Weekly Public Playlist',
                    'Description' => 'Public playlist, restarted weekly',
                ],
                $poll_properties, $poll_properties_public, $poll_properties_weekly
            ),
            'poll_members_weekly' =>array_merge([
                    'Name' => 'Weekly Members Playlist',
                    'Description' => 'Registered users playlist, restarted weekly',
                ],
                $poll_properties, $poll_properties_members, $poll_properties_weekly
            ),
            'poll_members_monthly' => array_merge([
                    'Name' => 'Monthly Members Playlist',
                    'Description' => 'Registered users playlist, restarted monthly',
                ],
                $poll_properties, $poll_properties_members, $poll_properties_monthly
            ),
            'poll_public_monthly_no_editable' => array_merge([
                    'Name' => 'Best Of "Weekly Public Playlist" Monthly',
                    'Description' => 'Playlist with the best song of each "Weekly Public Playlist" of this month',
                ],
                $poll_properties, $poll_properties_public, $poll_properties_no_editable, $poll_properties_monthly
            ),
            'poll_members_monthly_no_editable' => array_merge([
                    'Name' => 'Best Of "Weekly Members Playlist" Monthly',
                    'Description' => 'Playlist with best song of each "Weekly Members Playlist" of this month',
                ],
                $poll_properties, $poll_properties_members, $poll_properties_no_editable, $poll_properties_monthly
            ),
        ];
        $tracks = [
            [
                'Artist' => 'Lágrimas de Sangre',
                'Name' => 'Rojos y separatistas',
                'YoutubeUri' => 't67NhxJhrUU',
                'SpotifyUri' => '1ECc1EhfkRx08o8uIwYOxW',
            ],[
                'Artist' => 'Toti Soler',
                'Name' => 'Em Dius Que El Nostre Amor',
                'YoutubeUri' => 'rd55dcyjCSY',
                'SpotifyUri' => '5o31tm7aa5PdsThhw36it9',
            ],[
                'Artist' => 'Queen',
                'Name' => 'I Was Born To Love You - 2011 Remaster',
                'SpotifyUri' => '7DtdhIJlSSOaAFNk4JdXCb',
            ],[
                'Artist' => 'Queen',
                'Name' => 'Hammer To Fall - 2011 Remaster',
                'SpotifyUri' => '61lj5cHhOifNzSMXuWg54Z',
            ],[
                'Artist' => 'Giacomo Puccini',
                'Name' => 'Turandot / Act 3: "Nessun dorma!',
                'SpotifyUri' => '74WjYdm3Lvbwnds4thYPUU',
            ],[
                'Artist' => 'R. Kelly',
                'Name' => 'I Believe I Can Fly',
                'SpotifyUri' => '2RzJwBCXsS1VnjDm2jKKAa',
            ],[
                'Artist' => 'Zoufris Maracas',
                'Name' => 'Cocagne',
                'SpotifyUri' => '7FzdSwzenyUeiO6Dld2Y3v',
            ],[
                'Artist' => 'Rozalén',
                'Name' => 'La Puerta Violeta',
                'SpotifyUri' => '60kCg3tKhxb61QbBOFVzXh',
            ],[
                'Artist' => 'The Gramophone Allstars',
                'Name' => 'I Wish I Knew How It Would Feel To Be Free',
                'SpotifyUri' => '00POrfLzrW7hEtyAi1IeeM',
            ],[
                'Artist' => 'Tokyo Ska Paradise Orchestra',
                'Name' => '水琴窟-SUIKINKUTSU-',
                'SpotifyUri' => '6fOGYN4YLmaA5Yr6GjcDYV',
            ],[
                'Artist' => 'Michel Camilo',
                'Name' => 'Tropical Jam - Live',
                'SpotifyUri' => '4VJa1MNSiS5M1SkpdCNgxN',
            ],[
                'Artist' => 'Portugal. The Man',
                'Name' => 'Feel It Still',
                'SpotifyUri' => '6QgjcU0zLnzq5OrUoSZ3OK',
            ],[
                'Artist' => 'Inadaptats',
                'Name' => 'Orgull De Classe',
                'SpotifyUri' => '7dNYq25bervddvMWNe7Fqf',
            ],[
                'Artist' => 'Woodkid',
                'Name' => 'Run Boy Run',
                'SpotifyUri' => '0boS4e6uXwp3zAvz1mLxZS',
            ],[
                'Artist' => 'Chucho Valdés',
                'Name' => 'Caridad Amaro',
                'SpotifyUri' => '0qgfZBiE1XEGZuSESZTrIW',
            ],[
                'Artist' => 'The Skatalites',
                'Name' => 'Requiem for Rico',
                'SpotifyUri' => '7aSRHl639kQIa81lTDG7BD',
            ]
        ];
        $num_tracks = count($tracks);
        $num_polls_can_add_track = 0;

        // Create Polls
        $polls = [];
        foreach ($polls_schema as $poll_schema_key => $poll_schema) {
//            var_dump($poll_schema);
            $poll = new Poll($organization);
            foreach ($poll_schema as $property => $value) {
//                call_user_func(array($poll, 'set'.$property), $value);
                $poll->{'set'.$property}($value);
            }
            if ($poll->whoCanAddTrack() !== null) {
                $num_polls_can_add_track++;
            }
            $polls[$poll_schema_key] = $poll;
            unset($poll);
        }

        /**
         * @var Poll $poll
         */
        foreach ($polls as $poll) {

            // Add tracks
            $poll_sessions = $sessions;
            if ($poll->whoCanAddTrack() !== null) {

                $add_more_tracks = true;
                $user_can_add_more_tracks = true;
                while ($add_more_tracks && count($tracks) > 0) {
                    $track_info = $this->array_pop_rand($tracks);

                    if ($poll->whoCanAddTrack() > 3) { //Everyone can add tracks
                        if ($user_can_add_more_tracks && rand(0,1) === 0) { //50% member user, only one if multiple
                            $session = $user_session;
                            $user_can_add_more_tracks = $poll->isMultipleUserTracks();
                        } else { //50% anon user
                            if ($poll->isMultipleAnonTracks()) {
                                $session = $poll_sessions[rand(0, (count($poll_sessions)-1))];
                            } else {
                                $session = $this->array_pop_rand($poll_sessions);
                            }
                        }
                    } else { //Only members can add tracks
                        $session = $user_session;
                        $add_more_tracks = $poll->isMultipleUserTracks();
                    }

                    //Add track to poll
                    $track = new Track($poll, $session, $track_info['Artist'], $track_info['Name']);
                    $track->setSpotifyUri($track_info['SpotifyUri']);
                    if (isset($track_info['YoutubeUri'])) {
                        $track->setYoutubeUri($track_info['YoutubeUri']);
                    }
                    $poll->addTrack($track);

                    //Distribute the tracks along the polls
                    $max_num_tracks = intval(($num_tracks / $num_polls_can_add_track),10);
                    if ($poll->getNumTracks() > $max_num_tracks) {
                        $add_more_tracks = false;
                    }
                }
            }
            unset($track, $poll_sessions);

            // Add votes
            // If it's possible, user vote
            $poll_tracks = $poll->getTracks();
            $max_remaining_rating = $poll->getUserVotesMaxRating();
            while ($max_remaining_rating > 0 && count($poll_tracks) > 0) {
//                $track = array_pop($poll_tracks);
                $track = $this->array_pop_rand($poll_tracks);
                $rating = rand(0, $max_remaining_rating);
                $max_remaining_rating -= $rating;
                $vote = new Vote($poll, $track, $user_session, $rating);
                $poll->addVote($vote);
            }
            unset($track, $vote, $poll_tracks);

            //If it's possible, anon votes
            if ($poll->isAnonCanVote()) {
                $poll_sessions = $sessions;
                foreach ($poll_sessions as $poll_session) {

                    $poll_tracks = $poll->getTracks();
                    $max_remaining_rating = $poll->getAnonVotesMaxRating();
                    while ($max_remaining_rating > 0 && count($poll_tracks) > 0) {
//                        $track = array_pop($poll_tracks);
                        $track = $this->array_pop_rand($poll_tracks);
                        $rating = rand(0, $max_remaining_rating);
                        $max_remaining_rating -= $rating;
                        $vote = new Vote($poll, $track, $poll_session, $rating);
                        $poll->addVote($vote);
                    }
                }
                unset($track, $vote, $poll_tracks, $poll_sessions);
            }

//            \Doctrine\Common\Util\Debug::dump($poll);
        }

//        sleep(2);
//        $poll_finished->markAsEnded();

        foreach ($polls as $poll) {
            $manager->persist($poll);
        }

        $manager->flush();
    }

    private function array_pop_rand(&$array)
    {
        $array = array_values($array);
        $key = rand(0, (count($array)-1));
        $val = $array[$key];
        unset($array[$key]);
//        $array = array_values($array);
        return $val;
    }
}
