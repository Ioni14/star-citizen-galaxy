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
class LoanerShip
{
    /**
     * A ship can loan some others ship if it's not playable in-game. e.g., A Cyclone loans an Aurora MR.
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Ship", inversedBy="loanerShips")
     */
    private ?Ship $loaner = null;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Ship")
     * @ApiProperty(required=true)
     * @Groups({"ship:read"})
     * @MaxDepth(1)
     */
    private ?Ship $loaned = null;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     * @ApiProperty(required=true)
     * @Groups({"ship:read"})
     */
    private int $quantity;

    public function __construct(?Ship $loaner = null, ?Ship $loaned = null, int $quantity = 1)
    {
        $this->loaner = $loaner;
        $this->loaned = $loaned;
        $this->quantity = $quantity;
    }

    public function getLoaner(): ?Ship
    {
        return $this->loaner;
    }

    public function setLoaner(?Ship $loaner): self
    {
        $this->loaner = $loaner;

        return $this;
    }

    public function getLoaned(): ?Ship
    {
        return $this->loaned;
    }

    public function setLoaned(?Ship $loaned): self
    {
        $this->loaned = $loaned;

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
