<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *     @ORM\Index(name="label_idx", columns={"label"})
 * })
 *
 * @ApiResource(
 *   shortName="ShipFocuses",
 *   attributes={
 *     "pagination_items_per_page"=50,
 *     "normalization_context"={
 *       "groups"={"ship:read"}
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
class ShipFocus implements LockableEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ApiProperty(identifier=true)
     * @Groups({"ship:read"})
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string", length=30)
     * @ApiProperty(required=true)
     * @Groups({"ship:read"})
     */
    private string $label = '';

    public function __construct(?UuidInterface $id = null, string $label = '')
    {
        $this->id = $id;
        $this->label = $label;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
