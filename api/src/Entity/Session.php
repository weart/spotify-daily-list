<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="https://schema.org/AuthorizeAction",
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 *     collectionOperations={"post"},
 *     itemOperations={"put"},
 *     normalizationContext={"groups"={"session:read"}, "swagger_definition_name"="ReadSession"},
 *     denormalizationContext={"groups"={"session:write"}, "swagger_definition_name"="WriteSession"}
 * )
 * @ORM\Entity
 * @ORM\Table(name="sessions")
 */
class Session
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ApiProperty(identifier=true)
     * @Groups({"session:read", "track:read"})
     */
    private $id;

    /**
     * @var string|null The name of the user in this session, only used when $user is not defined
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/agent")
     * @Groups({"session:read", "session:write"})
     */
    private $name = null;

    /**
     * @var \DateTimeInterface The creation date of this session
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/startTime")
     * @Groups("session:read")
     */
    private $createdAt;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sessions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups("session:read")
     */
    private $user;

    /**
     * @var Track[] Tracks added in this session
     *
     * @ORM\OneToMany(targetEntity="Track", mappedBy="session")
     * @Groups("session:read")
     */
    private $tracks;

    /**
     * @var Vote[] Votes emitted in this session
     *
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="session")
     * @Groups("session:read")
     */
    private $votes;

    public function __construct(?string $name = null)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->setName($name);
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

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTracks(): array
    {
        return $this->tracks->getValues();
    }

    public function getTracksRaw(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            return $this->tracks->add($track);
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

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            return $this->votes->add($vote);
        }
        return true;
    }
}
