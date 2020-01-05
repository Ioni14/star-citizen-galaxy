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

    public function __construct(?string $name = null, ?string $code = null)
    {
        $this->name = $name;
        $this->code = $code;
    }
}
