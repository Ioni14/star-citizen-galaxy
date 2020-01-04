<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(name="name_idx", columns={"name"})
 * })
 * @UniqueEntity(fields={"slug"})
 */
class Ship
{
    public const READY_STATUS_FLIGHT_READY = 'flight-ready';
    public const READY_STATUS_CONCEPT = 'concept';
    public const READY_STATUSES = [
        self::READY_STATUS_FLIGHT_READY,
        self::READY_STATUS_CONCEPT,
    ];

    public const SIZE_VEHICLE = 'vehicle';
    public const SIZE_SNUB = 'snub';
    public const SIZE_SMALL = 'small';
    public const SIZE_MEDIUM = 'medium';
    public const SIZE_LARGE = 'large';
    public const SIZE_CAPITAL = 'capital';
    public const SIZES = [
        self::SIZE_VEHICLE,
        self::SIZE_SNUB,
        self::SIZE_SMALL,
        self::SIZE_MEDIUM,
        self::SIZE_LARGE,
        self::SIZE_CAPITAL,
    ];

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"ship:read"})
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"ship:read"})
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private string $slug = '';

    /**
     * @var ShipChassis
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipChassis")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"ship:read"})
     */
    private $chassis;

    /**
     * @var HoldedShip[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\HoldedShip", mappedBy="holded", fetch="EAGER", cascade={"all"}, orphanRemoval=true)
     */
    private $holders;

    /**
     * @var HoldedShip[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\HoldedShip", mappedBy="holder", fetch="EAGER", cascade={"all"}, orphanRemoval=true)
     * @Groups({"ship:read"})
     */
    private $holdedShips;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"ship:read"})
     */
    private ?float $height = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"ship:read"})
     */
    private ?float $length = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"ship:read"})
     */
    private ?int $maxCrew = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"ship:read"})
     */
    private ?int $minCrew = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"ship:read"})
     */
    private ?string $readyStatus = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"ship:read"})
     */
    private ?string $size = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"ship:read"})
     */
    private ?string $focus = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ship:read"})
     */
    private ?string $pledgeUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ship:read"})
     */
    private ?string $thumbnailPath = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ship:read"})
     */
    private ?string $picturePath = null;

    /**
     * In cents.
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"ship:read"})
     */
    private ?int $price = null;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Groups({"ship:read"})
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Groups({"ship:read"})
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private ?User $createdBy = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private ?User $updatedBy = null;

    public function __construct(?UuidInterface $id = null, ?ShipChassis $chassis = null)
    {
        $this->id = $id;
        $this->holders = new ArrayCollection();
        $this->holdedShips = new ArrayCollection();
        $this->chassis = $chassis ?? new ShipChassis();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getChassis(): ?ShipChassis
    {
        return $this->chassis;
    }

    public function setChassis(?ShipChassis $chassis): self
    {
        $this->chassis = $chassis;

        return $this;
    }

    /**
     * @return HoldedShip[]
     */
    public function getHoldedShips(): Collection
    {
        return $this->holdedShips;
    }

    public function clearHoldedShips(): void
    {
        $this->holdedShips->clear();
    }

    public function addHoldedShip(HoldedShip $holdedShip): self
    {
        $this->holdedShips->add($holdedShip);

        return $this;
    }

    public function removeHoldedShip(HoldedShip $holdedShip): self
    {
        $this->holdedShips->removeElement($holdedShip);

        return $this;
    }

    public function getHolders(): Collection
    {
        return $this->holders;
    }

    public function addHolder(HoldedShip $holdedShip): self
    {
        $this->holders->add($holdedShip);

        return $this;
    }

    public function removeHolder(HoldedShip $holdedShip): self
    {
        $this->holders->removeElement($holdedShip);

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getMaxCrew(): ?int
    {
        return $this->maxCrew;
    }

    public function setMaxCrew(?int $maxCrew): self
    {
        $this->maxCrew = $maxCrew;

        return $this;
    }

    public function getMinCrew(): ?int
    {
        return $this->minCrew;
    }

    public function setMinCrew(?int $minCrew): self
    {
        $this->minCrew = $minCrew;

        return $this;
    }

    public function getReadyStatus(): ?string
    {
        return $this->readyStatus;
    }

    public function setReadyStatus(?string $readyStatus): self
    {
        $this->readyStatus = $readyStatus;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getFocus(): ?string
    {
        return $this->focus;
    }

    public function setFocus(?string $focus): self
    {
        $this->focus = $focus;

        return $this;
    }

    public function getPledgeUrl(): ?string
    {
        return $this->pledgeUrl;
    }

    public function setPledgeUrl(?string $pledgeUrl): self
    {
        $this->pledgeUrl = $pledgeUrl;

        return $this;
    }

    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath;
    }

    public function setThumbnailPath(?string $thumbnailPath): self
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
