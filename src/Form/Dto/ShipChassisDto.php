<?php

namespace App\Form\Dto;

use App\Entity\Manufacturer;
use Symfony\Component\Validator\Constraints as Assert;

class ShipChassisDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="30")
     */
    public ?string $name;

    /**
     * @Assert\NotNull()
     */
    public ?Manufacturer $manufacturer;

    public function __construct(?string $name = null, ?Manufacturer $manufacturer = null)
    {
        $this->name = $name;
        $this->manufacturer = $manufacturer;
    }
}
