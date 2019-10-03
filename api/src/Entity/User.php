<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 * @ApiResource(
 *     iri="https://schema.org/Person",
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
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
     * @var string The name of the user
     *
     * @ORM\Column(type="string", unique=true, nullable=false, length=255)
     * @Assert\NotNull
     */
    private $username;

    /**
     * @var string The password of the user
     *
     * @ORM\Column(type="string", nullable=false, length=255)
     * @Assert\NotNull
     */
    private $password;

    /**
     * @var string The email of the user
     *
     * @ORM\Column(type="string", unique=true, nullable=false, length=255)
     * @Assert\Email(normalizer="trim", mode="loose")
     */
    private $email;

    /**
     * @var \DateTimeInterface The creation date of this user
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/startTime")
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface The last modification date of this user
     *
     * @ORM\Column(type="datetimetz", nullable=false)
     * @Assert\NotNull
     */
    private $updatedAt;

    /**
     * @var string Flag indicating if the user is active
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     * @Assert\NotNull
     */
    private $enabled = true;

    /**
     * @var bool Is this user visible to anyone?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":0})
     */
    private $publicVisibility = false;

    /**
     * @var bool Is the email of this user visible to anyone?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default":0})
     */
    private $publicEmail = false;

    CONST ADMIN = 1;
    CONST MEMBER = 2;

    public static function getValidRoles(): array
    {
        return [ self::ADMIN, self::MEMBER ];
    }

    public static function isValidRol(int $rol): bool
    {
        if(!in_array($rol, self::getValidRoles(), true)) {
            return false;
        }
        return true;
    }

    /**
     * @var array Roles of the user in this app: ADMIN = 1; MEMBER = 2;
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     * @Assert\NotNull
     */
    private $roles;

    /**
     * @var Session[] Sessions created by this user
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     */
    private $sessions;

    /**
     * @var Membership[] Organizations where this user belong
     *
     * @ORM\OneToMany(targetEntity="Membership", mappedBy="member")
     */
    private $memberships;

    /**
     * @var Track[] Tracks added by this user
     *
     * @ORM\OneToMany(targetEntity="Track", mappedBy="user")
     */
    private $tracks;

    /**
     * @var Vote[] Votes emitted by this user
     *
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="user")
     */
    private $votes;


    public function __construct(
        string $username, string $password, string $email,
        bool $enabled = true, bool $publicVisibility = false, bool $publicEmail = false,
        array $roles = [ self::MEMBER ]
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->setUsername($username);
        $this->password = $password; //@ToDO?
        $this->setEmail($email);
        $this->setEnabled($enabled);
        $this->setPublicVisibility($publicVisibility);
        $this->setPublicEmail($publicEmail);
        $this->setRoles($roles);
        $this->sessions = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->tracks = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist @ORM\PreUpdate
     */
    public function setUpdateAt()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        if (strlen($username) < 5) {
            throw new \InvalidArgumentException('Username too short, length must be >= 5 !');
        }
        //@ToDo: Check if the new username already exist in the ddbb, must be unique.
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function changePassword(string $old_password, string $new_password): bool
    {
        throw new \Exception('@ToDo');
    }

    public function getSalt(): string
    {
        return '';
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function hasPublicVisibility(): bool
    {
        return $this->publicVisibility;
    }

    public function setPublicVisibility(bool $publicVisibility): self
    {
        $this->publicVisibility = $publicVisibility;
        return $this;
    }

    public function hasPublicEmail(): bool
    {
        return $this->publicEmail;
    }

    public function setPublicEmail(bool $publicEmail): self
    {
        $this->publicEmail = $publicEmail;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = new ArrayCollection();
        foreach($roles as $rol) {
            $this->addRole($rol);
        }
        return $this;
    }

    public function addRole(int $rol): bool
    {
        if(!self::isValidRol($rol)) {
            throw new \InvalidArgumentException(sprintf('Rol %s is not valid', $rol));
        }

        if (!$this->roles->contains($rol)) {
            return $this->roles->add($rol);
        }
        return true;
    }

    public function getMemberships(): array
    {
        return $this->getMembershipsRaw()->getValues();
    }

    public function getMembershipsRaw(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Organization $organization): bool
    {
        if (!$this->getMembershipsRaw()->contains($organization)) {
            return $this->getMembershipsRaw()->add($organization);
        }
        return true;
    }

    public function removeMembership(Organization $organization): bool
    {
        return $this->getMembershipsRaw()->removeElement($organization);
    }

    public function getSessions(): array
    {
        return $this->getSessionsRaw()->getValues();
    }

    public function getSessionsRaw(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): bool
    {
        if (!$this->getSessionsRaw()->contains($session)) {
            return $this->getSessionsRaw()->add($session);
        }
        return true;
    }

    public function getTracks(): array
    {
        return $this->getTracksRaw()->getValues();
    }

    public function getTracksRaw(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): bool
    {
        if (!$this->getTracksRaw()->contains($track)) {
            return $this->getTracksRaw()->add($track);
        }
        return true;
    }

    public function removeTrack(Track $track): bool
    {
        return $this->getTracksRaw()->removeElement($track);
    }

    public function getVotes(): array
    {
        return $this->getVotesRaw()->getValues();
    }

    public function getVotesRaw(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): bool
    {
        if (!$this->getVotesRaw()->contains($vote)) {
            return $this->getVotesRaw()->add($vote);
        }
        return true;
    }

    public function removeVote(Vote $vote): bool
    {
        return $this->getVotesRaw()->removeElement($vote);
    }
}
