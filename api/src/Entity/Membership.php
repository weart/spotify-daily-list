<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A member of an Organization
 *
 * @ApiResource(
 *     iri="https://schema.org/ProgramMembership",
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 *     collectionOperations={"post"},
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete",
 *     },
 *     normalizationContext={"groups"={"membership:read"}, "swagger_definition_name"="ReadMembership"},
 *     denormalizationContext={"groups"={"membership:write"}, "swagger_definition_name"="WriteMembership"}
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
     * @Groups({"organization:read", "membership:read", "organization:readAll"})
     */
    private $id;

    /**
     * @var \DateTimeInterface DateTime when this object is created
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     * @Groups({"membership:read", "organization:readAll"})
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
     * @Groups({"membership:read", "membership:write", "organization:readAll"})
     */
    private $rol;

    /**
     * @var User User in this organization
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="memberships")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/member")
     * @Groups({"membership:read", "organization:readAll"})
     */
    private $member;

    /**
     * @var Organization Organization where this user belong
     *
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="memberships")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull
     * @ApiProperty(iri="http://schema.org/hostingOrganization")
     * @Groups("membership:read")
     */
    private $organization;


    public function __construct(User $member, Organization $organization, int $rol)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->organization = $organization;
        $this->member = $member;
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
