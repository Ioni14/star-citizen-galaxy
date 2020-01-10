<?php

namespace App\Form\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    /**
     * @Assert\File(maxSize="5M", mimeTypes={"image/jpeg", "image/png", "image/webp", "image/svg+xml"}, mimeTypesMessage="manufacturer.constraints.logo.bad_format")
     */
    public ?UploadedFile $logo = null;

    public ?string $logoPath;

    public int $lastVersion;

    /**
     * @Assert\Expression("value === this.lastVersion", message="Someone has modified this manufacturer in the meantime. Please refresh the page without submitting the form then apply your changes.")
     */
    public ?int $version = null;

    public function __construct(?string $name = null, ?string $code = null, ?string $logoPath = null, int $lastVersion = 0, int $version = 0)
    {
        $this->name = $name;
        $this->code = $code;
        $this->logoPath = $logoPath;
        $this->lastVersion = $lastVersion;
        $this->version = $version;
    }
}
