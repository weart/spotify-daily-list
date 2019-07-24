<?php

namespace App\DataFixtures;

use App\Entity\Poll;
use App\Entity\Track;
use App\Entity\Vote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Create a finished poll
        $poll_finished = new Poll();
        sleep(2);
        $poll_finished->finishPoll();

        //Create a ongoing poll
        $poll_open = new Poll();

        $polls = [$poll_finished, $poll_open];

        /**
         * @var Poll $poll
         */
        foreach($polls as $poll) {

            //Add track and votes
            $track = new Track();
            $track->poll = $poll;
            $track->artist = "Lágrimas de Sangre";
            $track->name = "Rojos y separatistas";
            $track->youtube_uri = "t67NhxJhrUU";
            $track->spotify_uri = "spotify:track:1ECc1EhfkRx08o8uIwYOxW";
            $poll->addTrack($track);

            //Add Votes
            for ($i = 0; $i < 10; $i++) {
                $vote = new Vote();
                $vote->poll = $poll;
                $vote->track = $track;
                $vote->name = 'Voter '.$i;
                $vote->rating = rand(0,5);
                $poll->addVote($vote);
            }

            $track = new Track();
            $track->poll = $poll;
            $track->artist = "Toti Soler";
            $track->name = "Em Dius Que El Nostre Amor";
            $track->youtube_uri = "rd55dcyjCSY";
            $track->spotify_uri = "spotify:track:5o31tm7aa5PdsThhw36it9";
            $poll->addTrack($track);

            //Add Votes
            for ($i = 0; $i < 10; $i++) {
                $vote = new Vote();
                $vote->poll = $poll;
                $vote->track = $track;
                $vote->name = 'Voter '.$i;
                $vote->rating = rand(0,5);
                $poll->addVote($vote);
            }
//            \Doctrine\Common\Util\Debug::dump($poll);

            $manager->persist($poll);
        }

        $manager->flush();
    }
}
