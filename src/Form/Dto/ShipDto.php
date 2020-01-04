<?php

namespace App\Form\Dto;

use App\Entity\ShipChassis;
use Symfony\Component\Validator\Constraints as Assert;

class ShipDto
{
    /**
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @Assert\NotNull()
     */
    public ?ShipChassis $chassis = null;

    /**
     * @Assert\Choice(choices=\App\Entity\Ship::SIZES)
     */
    public ?string $size = null;
}
