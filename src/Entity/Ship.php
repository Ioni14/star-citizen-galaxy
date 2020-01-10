<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(name="name_idx", columns={"name"})
 * })
 * @UniqueEntity(fields={"slug"})
 * @Gedmo\Loggable()
 *
 * @ApiResource(
 *   attributes={
 *     "pagination_items_per_page"=50,
 *     "normalization_context"={
 *       "groups"={"ship:read"},
 *       "enable_max_depth"=true
 *     },
 *     "force_eager"=false
 *   },
 *   collectionOperations={
 *     "get"
 *   },
 *   itemOperations={
 *     "get"
 *   }
 * )
 */
class Ship implements LockableEntityInterface
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
     * @ApiProperty(identifier=true)
     * @Groups({"ship:read"})
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string", length=30)
     * @Gedmo\Versioned()
     * @ApiProperty(iri="https://schema.org/name", required=true)
     * @Groups({"ship:read"})
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     */
    private ?string $slug = null;

    /**
     * @var ShipChassis
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipChassis")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     * @ApiProperty(required=true)
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
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private $holdedShips;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private ?float $height = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private ?float $length = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private ?int $maxCrew = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private ?int $minCrew = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={"type"="string", "enum"=Ship::READY_STATUSES},
     *         "openapi_context"={"type"="string", "enum"=Ship::READY_STATUSES}
     *     }
     * )
     * @Groups({"ship:read"})
     */
    private ?string $readyStatus = null;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={"type"="string", "enum"=Ship::SIZES},
     *         "openapi_context"={"type"="string", "enum"=Ship::SIZES}
     *     }
     * )
     * @Groups({"ship:read"})
     */
    private ?string $size = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private ?int $cargoCapacity = null;

    /**
     * @var ShipCareer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipCareer", fetch="EAGER")
     * @Gedmo\Versioned()
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private ?ShipCareer $career = null;

    /**
     * @var ShipRole[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ShipRole", fetch="EAGER")
     * @ApiProperty()
     * @Groups({"ship:read"})
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty(iri="https://schema.org/url")
     * @Groups({"ship:read"})
     */
    private ?string $pledgeUrl = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private ?string $thumbnailPath = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private ?string $picturePath = null;

    /**
     * @ApiProperty(iri="https://schema.org/image")
     * @Groups({"ship:read"})
     */
    private ?string $pictureUri = null;

    /**
     * @ApiProperty(iri="https://schema.org/image")
     * @Groups({"ship:read"})
     */
    private ?string $thumbnailUri = null;

    /**
     * In cents.
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty(iri="https://schema.org/price")
     * @Groups({"ship:read"})
     */
    private ?int $standalonePrice = null;

    /**
     * In cents.
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned()
     * @ApiProperty(iri="https://schema.org/price")
     * @Groups({"ship:read"})
     */
    private ?int $warbondPrice = null;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Gedmo\Timestampable(on="create")
     * @ApiProperty(iri="https://schema.org/DateTime")
     * @Groups({"ship:read"})
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Gedmo\Timestampable(on="update")
     * @ApiProperty(iri="https://schema.org/DateTime")
     * @Groups({"ship:read"})
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Gedmo\Blameable(on="create")
     */
    private ?User $createdBy = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Gedmo\Blameable(on="update")
     */
    private ?User $updatedBy = null;

    public function __construct(?UuidInterface $id = null, ?ShipChassis $chassis = null)
    {
        $this->id = $id;
        $this->holders = new ArrayCollection();
        $this->holdedShips = new ArrayCollection();
        $this->roles = new ArrayCollection();
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

    public function getSlug(): ?string
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

    public function getCargoCapacity(): ?int
    {
        return $this->cargoCapacity;
    }

    public function setCargoCapacity(?int $cargoCapacity): self
    {
        $this->cargoCapacity = $cargoCapacity;

        return $this;
    }

    public function getCareer(): ?ShipCareer
    {
        return $this->career;
    }

    public function setCareer(?ShipCareer $career): self
    {
        $this->career = $career;

        return $this;
    }

    /**
     * @return ShipRole[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function clearRoles(): void
    {
        $this->roles->clear();
    }

    public function addRole(ShipRole $role): self
    {
        $this->roles->add($role);

        return $this;
    }

    public function removeRole(ShipRole $role): self
    {
        $this->roles->removeElement($role);

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

    public function getPictureUri(): ?string
    {
        return $this->pictureUri;
    }

    public function getThumbnailUri(): ?string
    {
        return $this->thumbnailUri;
    }

    public function getStandalonePrice(): ?int
    {
        return $this->standalonePrice;
    }

    public function setStandalonePrice(?int $standalonePrice): self
    {
        $this->standalonePrice = $standalonePrice;

        return $this;
    }

    public function getWarbondPrice(): ?int
    {
        return $this->warbondPrice;
    }

    public function setWarbondPrice(?int $warbondPrice): self
    {
        $this->warbondPrice = $warbondPrice;

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
