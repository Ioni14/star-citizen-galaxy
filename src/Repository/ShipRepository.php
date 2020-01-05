<?php

namespace App\Repository;

use App\Entity\Ship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ship::class);
    }

    public function findShipJoinedChassis(): array
    {
        $dql = <<<DQL
            SELECT s, c, m, s2 FROM App\Entity\Ship s
            JOIN s.chassis c
            JOIN c.manufacturer m
            LEFT JOIN s.holdedShips s2
            DQL;
        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    public function findOneShipJoinedChassis(string $slug): ?Ship
    {
        $dql = <<<DQL
            SELECT s, c, m, s2 FROM App\Entity\Ship s
            JOIN s.chassis c
            JOIN c.manufacturer m
            LEFT JOIN s.holdedShips s2
            WHERE s.slug = :slug
            DQL;
        $query = $this->_em->createQuery($dql);
        $query->setParameter('slug', $slug);

        return $query->getOneOrNullResult();
    }
}
