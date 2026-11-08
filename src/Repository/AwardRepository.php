<?php

namespace App\Repository;

use App\Entity\Award;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Award>
 */
class AwardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Award::class);
    }

    public function setAsDefault(Award $award): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $connection->transactional(function () use ($award): void {
            $this->unsetAllDefault();

            $this->createQueryBuilder('a')
                ->update()
                ->set('a.isDefault', ':value')
                ->where('a.id = :id')
                ->setParameter('value', true)
                ->setParameter('id', $award->getId())
                ->getQuery()
                ->execute();
        });
    }

    public function unsetAllDefault(): void
    {
        $this->createQueryBuilder('a')
            ->update()
            ->set('a.isDefault', ':value')
            ->setParameter('value', false)
            ->getQuery()
            ->execute();
    }

    public function findDefault(): ?Award
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isDefault = :value')
            ->setParameter('value', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
