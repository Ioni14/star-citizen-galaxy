<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByDiscordId(string $id): ?User
    {
        $dql = <<<DQL
            SELECT u FROM App\Entity\User u
            WHERE u.discordId = :id
            DQL;
        $query = $this->_em->createQuery($dql);
        $query->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }
}
