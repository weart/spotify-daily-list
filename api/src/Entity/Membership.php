<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A member of an Organization
 *
 * @ApiResource(
 *     iri="https://schema.org/ProgramMembership",
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 *     collectionOperations={"get"},
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete",
 *     }
 * )
 * @ORM\Entity
 * @ORM\Table(name="memberships")
 */
class Membership
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
     */
    private $createdAt;

    CONST OWNER = 0;
    CONST ADMIN = 1;
    CONST MEMBER = 2;
    CONST INVITED = 3;

    public static function getValidRoles(): array
    {
        return [ self::OWNER, self::ADMIN, self::MEMBER, self::INVITED ];
    }

    public static function isValidRol(int $rol): bool
    {
        if(!in_array($rol, self::getValidRoles(), true)) {
            return false;
        }
        return true;
    }

    /**
     * @var array Rol of the user in this organization: OWNER = 0;ADMIN = 1;MEMBER = 2;INVITED = 3;
     *
     * @ORM\Column(type="smallint", nullable=false)
     * @Assert\NotNull
     */
    private $rol;

    /**
     * @var User User in this organization
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="memberships")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/member")
     */
    private $member;

    /**
     * @var Organization Organization where this user belong
     *
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="memberships")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/hostingOrganization")
     */
    private $organization;


    public function __construct(User $member, Organization $organization, int $rol)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->organization = $organization;
        $this->ḿember = $member;
        $this->setRol($rol);
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

    public function getRol(): int
    {
        return $this->rol;
    }

    public function getMember(): User
    {
        return $this->member;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    private function setRol(int $rol): self
    {
        if(!self::isValidRol($rol)) {
            throw new \InvalidArgumentException(sprintf('Rol %s is not valid', $rol));
        }
        $this->rol = $rol;
        return $this;
    }
}
