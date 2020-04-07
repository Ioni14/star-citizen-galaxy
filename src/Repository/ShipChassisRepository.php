<?php

namespace App\Repository;

use App\Entity\ShipChassis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

class ShipChassisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipChassis::class);
    }

    public function findChassisJoinedManufacturer(): array
    {
        $dql = <<<DQL
            SELECT sc, m FROM App\Entity\ShipChassis sc
            JOIN sc.manufacturer m
            DQL;
        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    public function findOneChassisJoinedManufacturer(string $slug): ?ShipChassis
    {
        $dql = <<<DQL
            SELECT sc, m FROM App\Entity\ShipChassis sc
            JOIN sc.manufacturer m
            WHERE sc.slug = :slug
            DQL;
        $query = $this->_em->createQuery($dql);
        $query->setParameter('slug', $slug);

        return $query->getOneOrNullResult();
    }

    public function countChassisByManufacturer(UuidInterface $manufacturerId): int
    {
        $query = $this->_em->createQuery('SELECT COUNT(chassis) FROM App\Entity\ShipChassis chassis WHERE chassis.manufacturer = :manufacturer');
        $query->setParameter('manufacturer', $manufacturerId);

        return $query->getSingleScalarResult();
    }
}
