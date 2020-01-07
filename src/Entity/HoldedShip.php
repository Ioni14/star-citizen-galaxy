<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity()
 * @ApiResource(
 *   attributes={
 *     "normalization_context"={
 *       "groups"={"ship:read"}
 *     },
 *     "force_eager"=false
 *   },
 *   collectionOperations={},
 *   itemOperations={
 *     "get"
 *   }
 * )
 */
class HoldedShip
{
    /**
     * A ship can contain some others ship. e.g., A Carrack includes a Pisces and a Rover.
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Ship", inversedBy="holdedShips")
     */
    private ?Ship $holder = null;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Ship", inversedBy="holders")
     * @ApiProperty(required=true)
     * @Groups({"ship:read"})
     * @MaxDepth(1)
     */
    private ?Ship $holded = null;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     * @ApiProperty(required=true)
     * @Groups({"ship:read"})
     */
    private int $quantity;

    public function __construct(?Ship $holder = null, ?Ship $holded = null, int $quantity = 1)
    {
        $this->holder = $holder;
        $this->holded = $holded;
        $this->quantity = $quantity;
    }

    public function getHolder(): ?Ship
    {
        return $this->holder;
    }

    public function setHolder(?Ship $holder): self
    {
        $this->holder = $holder;

        return $this;
    }

    public function getHolded(): ?Ship
    {
        return $this->holded;
    }

    public function setHolded(?Ship $holded): self
    {
        $this->holded = $holded;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
