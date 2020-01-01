<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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

    public const SIZE_VEHICLE = 'vehicle';
    public const SIZE_SNUB = 'snub';
    public const SIZE_SMALL = 'small';
    public const SIZE_MEDIUM = 'medium';
    public const SIZE_LARGE = 'large';
    public const SIZE_CAPITAL = 'capital';

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary", unique=true)
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private string $slug = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipChassis")
     * @ORM\JoinColumn(nullable=false)
     */
    private ShipChassis $chassis;

    /**
     * A ship can contain some others ship. e.g., A Carrack includes a Pisces.
     *
     * @ORM\ManyToOne(targetEntity="Ship", inversedBy="holdedShips")
     */
    private ?Ship $holder = null;

    /**
     * @ORM\OneToMany(targetEntity="Ship", mappedBy="holder")
     */
    private Collection $holdedShips;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $rsiId = null;

    /**
     * @ORM\Column(type="decimal", scale=4, nullable=true)
     */
    private ?float $height = null;

    /**
     * @ORM\Column(type="decimal", scale=4, nullable=true)
     */
    private ?float $length = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $maxCrew = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $minCrew = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private ?string $readyStatus = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private ?string $size = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private ?string $focus = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $pledgeUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $thumbnailUri = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $pictureUri = null;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable")
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

    public function getHolder(): ?self
    {
        return $this->holder;
    }

    public function setHolder(?self $holder): self
    {
        $this->holder = $holder;
        if ($holder !== null && !$holder->holdedShips->contains($this)) {
            $holder->addHoldedShip($this);
        }

        return $this;
    }

    public function getHoldedShips(): Collection
    {
        return $this->holdedShips;
    }

    public function addHoldedShip(self $ship): self
    {
        $this->holdedShips->add($ship);
        if ($ship->holder !== $this) {
            $ship->setHolder($this);
        }

        return $this;
    }

    public function removeHoldedShip(self $ship): self
    {
        $this->holdedShips->removeElement($ship);
        $ship->setHolder(null);

        return $this;
    }

    public function getRsiId(): ?int
    {
        return $this->rsiId;
    }

    public function setRsiId(?int $rsiId): self
    {
        $this->rsiId = $rsiId;

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

    public function getThumbnailUri(): ?string
    {
        return $this->thumbnailUri;
    }

    public function setThumbnailUri(?string $thumbnailUri): self
    {
        $this->thumbnailUri = $thumbnailUri;

        return $this;
    }

    public function getPictureUri(): ?string
    {
        return $this->pictureUri;
    }

    public function setPictureUri(?string $pictureUri): self
    {
        $this->pictureUri = $pictureUri;

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
