<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Track
 *
 * @ApiResource(
 *     iri="https://schema.org/MusicRecording",
 *     collectionOperations={},
 *     itemOperations={"get","put","delete"}
 * )
 * @ORM\Entity
 */
class Track
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string Spotify resource identifier
     * @example spotify:track:6rqhFgbbKwnb9MLmUQDhG6
     * @see https://developer.spotify.com/documentation/web-api/#spotify-uris-and-ids
     * @see https://developer.spotify.com/documentation/web-api/reference/tracks/get-track/
     *
     * @ORM\Column
     */
    public $spotify_uri;

    /**
     * @var string Youtube resource identifier
     * @example i6hoZqppsR8
     *
     * @ORM\Column
     */
    public $youtube_uri;

    /**
     * @var string The name of the artist
     *
     * @ORM\Column
     * @Assert\NotNull
     */
    public $artist;

    /**
     * @var string The name of the track
     *
     * @ORM\Column
     * @Assert\NotNull
     */
    public $name;

    /**
     * @var \DateTimeInterface The proposal date of this track
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     */
    private $proposalDate;


    /**
     * @var Poll The poll where track is participating
     *
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="tracks")
     * @Assert\NotNull
     */
    public $poll;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->proposalDate = new \DateTime();
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProposalDate(): \DateTime
    {
        return $this->proposalDate;
    }
}
