<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
//use App\Controller\FinishPollController;
use App\Controller\SpotifyController;

/**
 * Poll / Election / Referendum
 *
 * @ApiResource(
 *     iri="https://schema.org/Question",
 *     collectionOperations={"get","post"},
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete",
 *         "finish"={"path"="/polls/{id}/finish", "method"="PUT"},
 *         "finish"={"path"="/polls/{id}/restart", "method"="PUT"},
 *     }
 * )
 * @ORM\Entity
 */
class Poll
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string The name of the track
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var \DateTimeInterface The start date of this poll
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     */
    private $startDate;

    /**
     * @var \DateTimeInterface|null The end date of this poll
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string Spotify resource identifier
     * @example 5IKFuffeFlNxAAvzjSVUsZ
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(nullable=true)
     */
    public $spotifyPlaylistUri;

    /**
     * @var bool The poll is never closed, the winner song goes to winner playlist and the others to the historic playlist
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" : 0})
     */
    public $restartPoll;

    /**
     * @var string Spotify resource identifier
     * @example 5IKFuffeFlNxAAvzjSVUsZ
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(nullable=true)
     */
    public $spotifyWinnerPlaylistUri;

    /**
     * @var string Spotify resource identifier
     * @example 5IKFuffeFlNxAAvzjSVUsZ
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(nullable=true)
     */
    public $spotifyHistoricPlaylistUri;

    /**
     * @var Track[] Available tracks for this poll
     *
     * @ORM\OneToMany(targetEntity="Track", mappedBy="poll", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    public $tracks;

    /**
     * @var Vote[] Available votes for this poll
     *
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="poll", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    public $votes;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->startDate = new \DateTime();
        $this->name = sprintf('Poll created at %s', $this->startDate->format('d/m/Y H:i'));
        $this->restartPoll = false;
        $this->tracks = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function getSpotifyUri(): string
    {
        return $this->spotifyPlaylistUri ?
            sprintf('spotify:playlist:%s', $this->spotifyPlaylistUri) : '';
    }

    public function markAsEnded(): void
    {
        $this->endDate = new \DateTime();
    }

    public function addTrack(Track $track): bool
    {
        return $this->tracks->add($track);
    }

    public function addVote(Vote $vote): bool
    {
        return $this->votes->add($vote);
    }

    public function getTracks(): array
    {
        return $this->tracks->getValues();
    }

    public function getVotes(): array
    {
        return $this->votes->getValues();
    }

    public function getTrackOrderByVoted(): array
    {
        foreach ($this->tracks as $track) {
            $track->rating = 0;
        }
        foreach ($this->votes as $vote) {
            $vote->track->rating += $vote->rating;
//            $track = $this->tracks->get($vote->track);
//            $track->rating += $vote->rating;
        }

        $it = $this->tracks->getIterator();
        $it->uasort(function ($a, $b) {
            return (int) $a->rating > (int) $b->rating ? 1 : -1;
        });
        return $it->getArrayCopy();
    }
}
