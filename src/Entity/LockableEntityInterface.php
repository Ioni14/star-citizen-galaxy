<?php

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

interface LockableEntityInterface
{
    public function getId(): ?UuidInterface;
}
