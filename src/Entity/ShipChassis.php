<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShipChassisRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(name="name_idx", columns={"name"})
 * })
 * @UniqueEntity(fields={"slug"})
 * @Gedmo\Loggable()
 *
 * @ApiResource(
 *   shortName="Chassis",
 *   attributes={
 *     "pagination_items_per_page"=50,
 *     "normalization_context"={
 *       "groups"={"chassis:read"}
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
class ShipChassis implements LockableEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ApiProperty(identifier=true)
     * @Groups({"chassis:read", "ship:read"})
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Versioned()
     * @ApiProperty(
     *     iri="https://schema.org/name",
     *     required=true
     * )
     * @Groups({"chassis:read", "ship:read"})
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     */
    private ?string $slug = null;

    /**
     * @var Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned()
     * @ApiProperty(
     *     required=true
     * )
     * @Groups({"chassis:read", "ship:read"})
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Gedmo\Timestampable(on="create")
     * @ApiProperty(iri="https://schema.org/DateTime")
     * @Groups({"chassis:read"})
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Gedmo\Timestampable(on="update")
     * @ApiProperty(iri="https://schema.org/DateTime")
     * @Groups({"chassis:read"})
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

    public function __construct(?UuidInterface $id = null, string $name = '', ?string $slug = null, ?Manufacturer $manufacturer = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->manufacturer = $manufacturer ?? new Manufacturer();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(?UuidInterface $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

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
