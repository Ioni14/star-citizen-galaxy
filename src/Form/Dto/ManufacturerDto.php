<?php

namespace App\Form\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ManufacturerDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="30")
     */
    public ?string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="10")
     */
    public ?string $code;

    public int $lastVersion;

    /**
     * @Assert\Expression("value === this.lastVersion", message="Someone has modified this manufacturer in the meantime. Please refresh the page without submitting the form then apply your changes.")
     */
    public ?int $version = null;

    public function __construct(?string $name = null, ?string $code = null, int $lastVersion = 0, int $version = 0)
    {
        $this->name = $name;
        $this->code = $code;
        $this->lastVersion = $lastVersion;
        $this->version = $version;
    }
}
