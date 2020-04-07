<?php

namespace App\Repository;

use App\Entity\Ship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

class ShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ship::class);
    }

    /**
     * @return Ship[]
     */
    public function findShipJoinedChassis(): array
    {
        $dql = <<<DQL
            SELECT s, c, m, roles, career, createdBy, updatedBy FROM App\Entity\Ship s
            JOIN s.chassis c
            JOIN c.manufacturer m
            LEFT JOIN s.career career
            LEFT JOIN s.roles roles
            LEFT JOIN s.createdBy createdBy
            LEFT JOIN s.updatedBy updatedBy
            DQL;
        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    public function findOneShipJoinedChassis(string $slug): ?Ship
    {
        $dql = <<<DQL
            SELECT s, c, m, holdedShips, holdedShip, loanerShips, loanedShip, roles, career, createdBy, updatedBy FROM App\Entity\Ship s
            JOIN s.chassis c
            JOIN c.manufacturer m
            LEFT JOIN s.holdedShips holdedShips
            LEFT JOIN holdedShips.holded holdedShip
            LEFT JOIN s.loanerShips loanerShips
            LEFT JOIN loanerShips.loaned loanedShip
            LEFT JOIN s.career career
            LEFT JOIN s.roles roles
            LEFT JOIN s.createdBy createdBy
            LEFT JOIN s.updatedBy updatedBy
            WHERE s.slug = :slug
            DQL;
        $query = $this->_em->createQuery($dql);
        $query->setParameter('slug', $slug);

        return $query->getOneOrNullResult();
    }

    public function countShipsByChassis(UuidInterface $chassisId): int
    {
        $query = $this->_em->createQuery('SELECT COUNT(ship) FROM App\Entity\Ship ship WHERE ship.chassis = :chassis');
        $query->setParameter('chassis', $chassisId);

        return $query->getSingleScalarResult();
    }

    /**
     * @return Ship[]
     */
    public function searchByQuery(string $searchQuery): array
    {
        $like = '%'.$searchQuery.'%';

        $dql = <<<DQL
            SELECT ship FROM App\Entity\Ship ship
            WHERE ship.name LIKE :searchQuery
            ORDER BY ship.name
            DQL;
        $query = $this->_em->createQuery($dql);
        $query->setParameter('searchQuery', $like);

        return $query->getResult();
    }
}
