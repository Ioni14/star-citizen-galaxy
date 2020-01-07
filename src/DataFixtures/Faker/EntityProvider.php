<?php

namespace App\DataFixtures\Faker;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Faker\Provider\Base;

class EntityProvider extends Base
{
    private EntityManagerInterface $entityManager;

    public function __construct(Generator $generator, EntityManagerInterface $entityManager)
    {
        parent::__construct($generator);
        $this->entityManager = $entityManager;
    }

    public function findEntity(string $fqcn, array $criteria): ?object
    {
        return $this->entityManager->getRepository($fqcn)->findOneBy($criteria);
    }
}
