<?php

namespace App\Repository;

use App\Entity\Manufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ManufacturerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manufacturer::class);
    }

    /**
     * @return Manufacturer[]
     */
    public function searchByQuery(string $searchQuery): array
    {
        $like = '%'.$searchQuery.'%';

        $dql = <<<DQL
            SELECT manufacturer FROM App\Entity\Manufacturer manufacturer
            WHERE manufacturer.name LIKE :searchQuery OR manufacturer.code LIKE :searchQuery
            ORDER BY manufacturer.name
            DQL;
        $query = $this->_em->createQuery($dql);
        $query->setParameter('searchQuery', $like);

        return $query->getResult();
    }
}
