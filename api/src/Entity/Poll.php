<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

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
 * @ORM\Table(name="polls")
 */
class Poll
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ApiProperty(identifier=true)
     */
    private $id;

    /**
     * @var string The name of the poll
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/text")
     */
    private $name;

    /**
     * @var \DateTimeInterface The start date of this poll
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/datePublished")
     */
    private $startDate;

    /**
     * @var \DateTimeInterface|null The end date of this poll
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=true)
     * @ApiProperty(iri="http://schema.org/expires")
     */
    private $endDate;

    /**
     * @var string|null String with a crontab style restart command. If setted, this poll is never closed,
     * the winner song goes to winner playlist and the others to the historic playlist.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $restartDate;

    /**
     * @var array|null All the images from the Spotify playlist
     *
     * @ORM\Column(type="json", nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     */
    private $spotifyPlaylistImages;

    /**
     * @var string|null Spotify resource identifier
     * @example 5IKFuffeFlNxAAvzjSVUsZ
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/identifier")
     */
    private $spotifyPlaylistUri = null;

    /**
     * @var string|null Spotify resource identifier
     * @example 5IKFuffeFlNxAAvzjSVUsZ
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/identifier")
     */
    private $spotifyWinnerPlaylistUri = null;

    /**
     * @var string|null Spotify resource identifier
     * @example 5IKFuffeFlNxAAvzjSVUsZ
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/identifier")
     */
    private $spotifyHistoricPlaylistUri = null;

    /**
     * @var bool Is this poll visible to anyone or only to the members of the organization?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":0})
     */
    private $publicVisibility = false;

    /**
     * @var bool Can anyone vote into this poll or only the members of the organization?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    private $anonCanVote = true;

    /**
     * @var bool Can anyone add tracks into this poll or only members of the organization?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":0})
     */
    private $anonCanAddTrack = false;

    /**
     * @var int All the ratings given by a anonymous user to this poll can't excede this number
     *
     * @ORM\Column(type="smallint", nullable=false, options={"default":1})
     */
    private $anonVotesMaxRating = 1;

    /**
     * @var int All the ratings given by a member to this poll can't excede this number
     *
     * @ORM\Column(type="smallint", nullable=false, options={"default":10})
     */
    private $userVotesMaxRating = 10;

    /**
     * @var bool Can one user add more than one track to this poll?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    private $multipleUserTracks = true;

    /**
     * @var bool Can an anonymous user add more than one track to this poll?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    private $multipleAnonTracks = true;

    /**
     * @var Organization The organization owner of this poll
     *
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="polls")
     */
    private $organization;

    /**
     * @var Track[] Available tracks for this poll
     *
     * @ORM\OneToMany(targetEntity="Track", mappedBy="poll", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    private $tracks;

    /**
     * @var Vote[] Available votes for this poll
     *
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="poll", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    private $votes;

    public function __construct(Organization $organization)
    {
        $this->id = Uuid::uuid4();
        $this->startDate = new \DateTimeImmutable();
        $this->setName(sprintf(
            'Poll created at %s by %s',
            $this->getStartDate()->format('d/m/Y H:i'),
            $organization->getName()
        ));
        $this->organization = $organization;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function markAsEnded(): self
    {
        $this->endDate = new \DateTimeImmutable();
        return $this;
    }

    public function getRestartDate(): ?string
    {
        return $this->restartDate;
    }

    public function setRestartDate(?string $restartDate): self
    {
        //@ToDo: Check crontab style
        $this->restartDate = $restartDate;
        return $this;
    }

    public function getSpotifyPlaylistImages(): ?array
    {
        return $this->spotifyPlaylistImages;
    }

    public function setSpotifyPlaylistImages(array $spotifyPlaylistImages): self
    {
        $this->spotifyPlaylistImages = $spotifyPlaylistImages;
        return $this;
    }

    /**
     * Return spotify_playlist_uri value formatted with spotify format: spotify:playlist:%s
     */
    public function getSpotifyPlaylistUri(): ?string
    {
        return $this->spotifyPlaylistUri ?
            sprintf('spotify:playlist:%s', $this->spotifyPlaylistUri) : null;
    }

    /**
     * Return the raw spotify_playlist_uri value
     */
    public function getSpotifyPlaylistUriRaw(): ?string
    {
        return $this->spotifyPlaylistUri;
    }

    public function setSpotifyPlaylistUri(?string $spotifyPlaylistUri): self
    {
        $this->spotifyPlaylistUri = $spotifyPlaylistUri;
        return $this;
    }

    /**
     * Return spotify_winner_playlist_uri value formatted with spotify format: spotify:playlist:%s
     */
    public function getSpotifyWinnerPlaylistUri(): ?string
    {

        return $this->spotifyWinnerPlaylistUri ?
            sprintf('spotify:playlist:%s', $this->spotifyWinnerPlaylistUri) : null;
    }

    /**
     * Return the raw spotify_winner_playlist_uri value
     */
    public function getSpotifyWinnerPlaylistUriRaw(): ?string
    {
        return $this->spotifyWinnerPlaylistUri;
    }

    public function setSpotifyWinnerPlaylistUri(?string $spotifyWinnerPlaylistUri): self
    {
        $this->spotifyWinnerPlaylistUri = $spotifyWinnerPlaylistUri;
        return $this;
    }

    /**
     * Return spotify_historic_playlist_uri value formatted with spotify format: spotify:playlist:%s
     */
    public function getSpotifyHistoricPlaylistUri(): ?string
    {
        return $this->spotifyHistoricPlaylistUri ?
            sprintf('spotify:playlist:%s', $this->spotifyHistoricPlaylistUri) : null;
    }

    /**
     * Return the raw spotify_historic_playlist_uri value
     */
    public function getSpotifyHistoricPlaylistUriRaw(): ?string
    {
        return $this->spotifyHistoricPlaylistUri;
    }

    public function setSpotifyHistoricPlaylistUri(?string $spotifyHistoricPlaylistUri): self
    {
        $this->spotifyHistoricPlaylistUri = $spotifyHistoricPlaylistUri;
        return $this;
    }

    public function isPublicVisibility(): bool
    {
        return $this->publicVisibility;
    }

    public function setPublicVisibility(bool $publicVisibility): self
    {
        $this->publicVisibility = $publicVisibility;
        return $this;
    }

    public function isAnonCanVote(): bool
    {
        return $this->anonCanVote;
    }

    public function setAnonCanVote(bool $anonCanVote): self
    {
        $this->anonCanVote = $anonCanVote;
        return $this;
    }

    public function isAnonCanAddTrack(): bool
    {
        return $this->anonCanAddTrack;
    }

    public function setAnonCanAddTrack(bool $anonCanAddTrack): self
    {
        $this->anonCanAddTrack = $anonCanAddTrack;
        return $this;
    }

    public function getAnonVotesMaxRating(): int
    {
        return $this->anonVotesMaxRating;
    }

    public function setAnonVotesMaxRating(int $anonVotesMaxRating): self
    {
        $this->anonVotesMaxRating = $anonVotesMaxRating;
        return $this;
    }

    public function getUserVotesMaxRating(): int
    {
        return $this->userVotesMaxRating;
    }

    public function setUserVotesMaxRating(int $userVotesMaxRating): self
    {
        $this->userVotesMaxRating = $userVotesMaxRating;
        return $this;
    }

    public function isMultipleUserTracks(): bool
    {
        return $this->multipleUserTracks;
    }

    public function setMultipleUserTracks(bool $multipleUserTracks): self
    {
        $this->multipleUserTracks = $multipleUserTracks;
        return $this;
    }

    public function isMultipleAnonTracks(): bool
    {
        return $this->multipleAnonTracks;
    }

    public function setMultipleAnonTracks(bool $multipleAnonTracks): self
    {
        $this->multipleAnonTracks = $multipleAnonTracks;
        return $this;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getTracks(): array
    {
        return $this->tracks->getValues();
    }

    public function getTracksRaw(): Collection
    {
        return $this->tracks;
    }

    public function getTrackOrderByVoted(): array
    {
        foreach ($this->tracks as $track) {
            $track->rating = 0;
        }
        foreach ($this->votes as $vote) {
            $vote->getTrack()->rating += $vote->rating;
//            $track = $this->tracks->get($vote->track);
//            $track->rating += $vote->rating;
        }

        $it = $this->tracks->getIterator();
        $it->uasort(function ($a, $b) {
            return (int) $a->rating > (int) $b->rating ? 1 : -1;
        });
        return $it->getArrayCopy();
    }

    public function addTrack(Track $track): bool
    {
        if ($this->getEndDate()) {
            throw new \Exception('This poll is closed');
        }
        if (!$this->tracks->contains($track)) {
            return $this->tracks->add($track);
        }
        return true;
    }

    public function removeTrack(Track $track): bool
    {
        if ($this->getEndDate()) {
            throw new \Exception('This poll is closed');
        }
        return $this->tracks->removeElement($track);
    }

    public function addVote(Vote $vote): bool
    {
        if ($this->getEndDate()) {
            throw new \Exception('This poll is closed');
        }
        if (!$this->votes->contains($vote)) {
            return $this->votes->add($vote);
        }
        return true;
    }

    public function getVotes(): array
    {
        return $this->votes->getValues();
    }

    public function getVotesRaw(): Collection
    {
        return $this->votes;
    }

    public function truncatePoll(): self
    {
        if ($this->getEndDate()) {
            throw new \Exception('This poll is closed');
        }
        $this->tracks = new ArrayCollection();
        $this->votes = new ArrayCollection();
        return $this;
    }
}
