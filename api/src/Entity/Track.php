<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Track
 *
 * @ApiResource(
 *     iri="https://schema.org/MusicRecording",
 *     collectionOperations={},
 *     itemOperations={"get","put","delete"},
 *     normalizationContext={"groups"={"track:read"}, "swagger_definition_name"="ReadTrack"},
 *     denormalizationContext={"groups"={"track:write"}, "swagger_definition_name"="WriteTrack"}
 * )
 * @ORM\Entity
 * @ORM\Table(name="tracks")
 */
class Track
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ApiProperty(identifier=true)
     * @Groups({"track:read", "poll:readAll"})
     */
    private $id;

    /**
     * @var string The name of the artist
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/byArtist")
     * @Groups({"track:read", "track:write", "poll:readAll"})
     */
    private $artist;

    /**
     * @var string The name of the track
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/name")
     * @Groups({"track:read", "track:write", "poll:readAll"})
     */
    private $name;

    /**
     * @var \DateTimeInterface The proposal date of this track
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @Assert\NotNull
     * @Groups("track:read")
     */
    private $proposalDate;

    /**
     * @var string|null Spotify resource identifier
     * @example 6rqhFgbbKwnb9MLmUQDhG6
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/identifier")
     * @Groups({"track:read", "track:write", "poll:readAll"})
     */
    private $spotifyUri;

    /**
     * @var array|null All the images from the Spotify track
     *
     * @ORM\Column(type="json", nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     * @Groups({"track:read", "track:write", "poll:readAll"})
     */
    private $spotifyImages;

    /**
     * @var string|null Youtube resource identifier
     * @example i6hoZqppsR8
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/identifier")
     * @Groups({"track:read", "track:write", "poll:readAll"})
     */
    private $youtubeUri;

    /**
     * @var Poll The poll where track is participating
     *
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="tracks")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @Groups("track:read")
     */
    private $poll;

    /**
     * @var Session The Session who suggest this Track, used in anonymous sessions
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="tracks")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @Groups({"track:read", "track:write"})
     */
    private $session;

    /**
     * @var User|null The User who suggest this Track
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tracks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @Groups("track:read")
     */
    private $user;


    public function __construct(Poll $poll, Session $session, string $artist, string $name)
    {
        $this->id = Uuid::uuid4();
        $this->poll = $poll;
        $this->session = $session;
        if ($session->getUser()) {
            $this->user = $session->getUser();
        }
        $this->setArtist($artist);
        $this->setName($name);
        $this->proposalDate = new \DateTimeImmutable();
    }

    public function __toString()
    {
        return sprintf('%s - %s', $this->getArtist(), $this->getName());
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getArtist(): string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): self
    {
        $this->artist = $artist;
        return $this;
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

    public function getProposalDate(): \DateTimeImmutable
    {
        return $this->proposalDate;
    }

    public function getSpotifyUri(): ?string
    {
        return $this->spotifyUri ?
            sprintf('spotify:track:%s', $this->spotifyUri) : null;
    }

    public function getSpotifyUriRaw(): ?string
    {
        return $this->spotifyUri;
    }

    public function setSpotifyUri(?string $spotifyUri): self
    {
        //@ToDo: Check value
        $this->spotifyUri = $spotifyUri;
        return $this;
    }

    public function getSpotifyImages(): ?array
    {
        return $this->spotifyImages;
    }

    public function setSpotifyImages(?array $spotifyImages): self
    {
        //@ToDo: Check value
        $this->spotifyImages = $spotifyImages;
        return $this;
    }

    public function getYoutubeUri(): ?string
    {
        return $this->youtubeUri;
    }

    public function setYoutubeUri(?string $youtubeUri): self
    {
        //@ToDo: Check value
        $this->youtubeUri = $youtubeUri;
        return $this;
    }

    public function getPoll(): Poll
    {
        return $this->poll;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
