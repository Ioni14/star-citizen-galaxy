<?php

namespace App\Form\Dto;

use App\Entity\Manufacturer;
use Symfony\Component\Validator\Constraints as Assert;

class ShipChassisDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="1", max="50")
     */
    public ?string $name;

    /**
     * @Assert\NotNull()
     */
    public ?Manufacturer $manufacturer;

    public int $lastVersion;

    /**
     * @Assert\Expression("value === this.lastVersion", message="Someone has modified this Ship chassis in the meantime. Please refresh the page without submitting the form then apply your changes.")
     */
    public ?int $version = null;

    public function __construct(?string $name = null, ?Manufacturer $manufacturer = null, int $lastVersion = 0, int $version = 0)
    {
        $this->name = $name;
        $this->manufacturer = $manufacturer;
        $this->lastVersion = $lastVersion;
        $this->version = $version;
    }
}
