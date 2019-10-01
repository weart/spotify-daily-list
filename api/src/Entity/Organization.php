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
 * An organization such as a school, NGO, corporation, club, etc.
 *
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 * @ApiResource(
 *     iri="https://schema.org/Organization",
 *     collectionOperations={"get"},
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete",
 *     }
 * )
 * @ORM\Entity
 * @ORM\Table(name="organizations")
 */
class Organization
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
     * @var \DateTimeInterface DateTime when this object is created
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @ApiProperty(iri="http://schema.org/foundingDate")
     */
    private $createdAt;

    /**
     * @var string The name of the organization
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/name")
     */
    private $name;

    /**
     * @var bool Is this organization public and can be seen be anyone?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     * @Assert\NotNull
     */
    private $publicVisibility = false;

    /**
     * @var bool Can anyone be part of this organization? Or an invitation is required?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":0})
     * @Assert\NotNull
     */
    private $publicMembership = false;

    /**
     * @var bool Minimum membership rol for allow create new polls, by default: only Admin and Owner.
     *
     * @ORM\Column(type="smallint", nullable=false, options={"default":1})
     * @Assert\NotNull
     */
    private $canCreatePolls = Membership::ADMIN;

    /**
     * @var Membership[] Members in this organization
     *
     * @ORM\OneToMany(targetEntity="Membership", mappedBy="organization", cascade={"persist", "remove"})
     */
    private $memberships;

    /**
     * @var Poll[] Polls in this organization
     *
     * @ORM\OneToMany(targetEntity="Poll", mappedBy="organization", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    private $polls;


    public function __construct(
        string $name, bool $publicVisibility = false, bool $publicMembership = false, int $canCreatePolls = Membership::ADMIN
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->setName($name);
        $this->setPublicVisibility($publicVisibility);
        $this->setPublicMembership($publicMembership);
        $this->setWhoCanCreatePolls($canCreatePolls);
        $this->memberships = new ArrayCollection();
        $this->polls = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?: strval($this->getId());
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("Name can't be empty");
        }
        //Check if name already exists? Must be unique?
        $this->name = $name;
        return $this;
    }

    public function getPublicVisibility(): bool
    {
        return $this->publicVisibility;
    }

    public function setPublicVisibility(bool $publicVisibility): self
    {
        $this->publicVisibility = $publicVisibility;
        return $this;
    }

    public function getPublicMembership(): bool
    {
        return $this->publicMembership;
    }

    public function setPublicMembership(bool $publicMembership): self
    {
        $this->publicMembership = $publicMembership;
        return $this;
    }

    public function userCanCreatePolls(Membership $membership)
    {
        return $this->canCreatePolls <= $membership->getRol();
    }

    public function setWhoCanCreatePolls(int $rol): self
    {
        if(!Membership::isValidRol($rol)) {
            throw new \InvalidArgumentException(sprintf('Rol %s is not valid', $rol));
        }
        $this->canCreatePolls = $rol;
        return $this;
    }

    public function getMemberships(): array
    {
        return $this->getMembershipsRaw()->getValues();
    }

    public function getMembershipsRaw(): Collection
    {
        return $this->memberships;
    }

    public function getMembers(): array
    {
        return $this->getMembersRaw()->getValues();
    }

    public function getMembersRaw(): Collection
    {
        return $this->getMembershipsRaw()->map(function (Membership $membership) {
            $membership->getMember();
        });
    }

    public function getPolls(): array
    {
        return $this->getPollsRaw()->getValues();
    }

    public function getPollsRaw(): Collection
    {
        return $this->polls;
    }

    public function addPoll(Poll $poll): self
    {
        if (!$this->getPolls()->contains($poll)) {
            return $this->getPolls()->add($poll);
        }
        return true;
    }
}
