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
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $organization = new Organization('Discoveryfy');
        $manager->persist($organization);
        $user = new User('Leninux', 'abcd', 'discoverify@fabri.cat');
        $manager->persist($user);
        $membership = new Membership($user, $organization, Membership::OWNER);
        $manager->persist($membership);

        $sessions = [];
        for ($i = 0; $i < 10; $i++) {
            $session = new Session('Voter '.$i);
            $manager->persist($session);
            $sessions[$i] = $session;
        }
        ($sessions[0])->setUser($user);

        //Create a finished poll
        $poll_finished = new Poll($organization);

        //Create a ongoing poll
        $poll_open = new Poll($organization);

        $polls = [$poll_finished, $poll_open];

        /**
         * @var Poll $poll
         */
        foreach ($polls as $poll) {

            //Add Track with a User
            $artist = 'Lágrimas de Sangre';
            $name = 'Rojos y separatistas';
            $track = new Track($poll, $sessions[0], $artist, $name);
            $track->setYoutubeUri('t67NhxJhrUU');
            $track->setSpotifyUri('1ECc1EhfkRx08o8uIwYOxW');
            $poll->addTrack($track);

            //Add Votes with all sessions
            for ($i = 0; $i < 10; $i++) {
                $rating = rand(0,5);
                $vote = new Vote($poll, $track, $sessions[$i], $rating);
                $poll->addVote($vote);
            }

            //Add Track with anonymous session
            $track = new Track($poll, $sessions[1], 'Toti Soler', 'Em Dius Que El Nostre Amor');
            $track->setYoutubeUri('rd55dcyjCSY');
            $track->setSpotifyUri('5o31tm7aa5PdsThhw36it9');
            $poll->addTrack($track);

            //Add Votes with all sessions
            for ($i = 0; $i < 10; $i++) {
                $rating = rand(0,5);
                $vote = new Vote($poll, $track, $sessions[$i], $rating);
                $poll->addVote($vote);
            }
//            \Doctrine\Common\Util\Debug::dump($poll);
        }

        sleep(2);
        $poll_finished->markAsEnded();

        foreach ($polls as $poll) {
            $manager->persist($poll);
        }

        $manager->flush();
    }
}
