<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vote
 *
 * @ApiResource(
 *     iri="https://schema.org/Rating",
 *     collectionOperations={"get"},
 *     itemOperations={"get","put","delete"},
 *     normalizationContext={"groups"={"vote:read"}, "swagger_definition_name"="ReadVote"},
 *     denormalizationContext={"groups"={"vote:write"}, "swagger_definition_name"="WriteVote"}
 * )
 * @ORM\Entity
 * @ORM\Table(name="votes")
 */
class Vote
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ApiProperty(identifier=true)
     * @Groups({"vote:read", "poll:readAll"})
     */
    private $id;

    /**
     * @var \DateTimeInterface The creation date of this vote
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @Assert\NotNull
     * @Groups("vote:read")
     */
    private $createdAt;

    /**
     * @var int The rating of this vote
     *
     * @ORM\Column(type="smallint", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/ratingValue")
     * @Groups({"vote:read", "vote:write", "poll:readAll"})
     */
    public $rating;

    /**
     * @var Poll The poll this vote is about
     *
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="votes")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     */
    private $poll;

    /**
     * @var Track The track this vote is about
     *
     * @ORM\ManyToOne(targetEntity="Track")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     */
    private $track;

    /**
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="votes")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=false)
     */
    private $session;

    /**
     * @var User The user who emit this vote, optional
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="votes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;


    public function __construct(Poll $poll, Track $track, Session $session, int $rating)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->poll = $poll;
        $this->track = $track;
        $this->session = $session;
        if ($session->getUser()) {
            $this->user = $session->getUser();
        }
        //@ToDo: Ration validation
        $this->rating = $rating;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPoll(): Poll
    {
        return $this->poll;
    }

    public function getTrack(): Track
    {
        return $this->track;
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
