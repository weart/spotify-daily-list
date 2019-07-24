<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vote
 *
 * @ApiResource(
 *     iri="https://schema.org/Rating",
 *     collectionOperations={},
 *     itemOperations={"get","put","delete"}
 * )
 * @ORM\Entity
 *
 */
class Vote
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string The name of the voter
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var \DateTimeInterface The creation date of this vote
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     */
    private $creationDate;

    /**
     * @var int The rating of this vote (between 0 and 5).
     *
     * @ORM\Column(type="smallint")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=5)
     */
    public $rating;

    /**
     * @var Poll The poll this vote is about
     *
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="votes")
     * @Assert\NotNull
     */
    public $poll;

    /**
     * @var Track The track this vote is about
     *
     * @ORM\ManyToOne(targetEntity="Track")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     * @Assert\NotNull
     */
    public $track;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->creationDate = new \DateTime();
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }
}
