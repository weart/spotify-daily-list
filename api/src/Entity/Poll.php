<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Poll / Election / Referendum
 *
 * @ApiResource(
 *     iri="https://schema.org/Question",
 *     collectionOperations={"get","post"},
 *     itemOperations={"get","put","delete"}
 * )
 * @ORM\Entity
 */
class Poll
{
    /**
     * @var Uuid The entity Id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string The name of the track
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var \DateTimeInterface The start date of this poll
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     */
    private $startDate;

    /**
     * @var \DateTimeInterface|null The end date of this poll
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $endDate;


    /**
     * @var Track[] Available tracks for this poll
     *
     * @ORM\OneToMany(targetEntity="Track", mappedBy="poll", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    public $tracks;

    /**
     * @var Vote[] Available votes for this poll
     *
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="poll", cascade={"persist", "remove"})
     * @ApiSubresource
     */
    public $votes;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->startDate = new \DateTime();
        $this->name = sprintf('Poll created at %s', $this->startDate->format('d/m/Y H:i'));
        $this->tracks = $this->votes = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function finishPoll(): void
    {
        $this->endDate = new \DateTime();
    }
}
