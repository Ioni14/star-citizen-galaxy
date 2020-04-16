<?php

namespace App\Form\Dto;

use App\Entity\Ship;
use Symfony\Component\Validator\Constraints as Assert;

class LoanerShipDto
{
    /**
     * @Assert\NotNull()
     */
    public ?Ship $ship;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="2147483647")
     */
    public ?int $quantity;

    public function __construct(?Ship $ship = null, ?int $quantity = 1)
    {
        $this->ship = $ship;
        $this->quantity = $quantity;
    }
}
