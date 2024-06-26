<?php

namespace App\Repository;

use App\Entity\LegalEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LegalEntity>
 *
 * @method LegalEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalEntity[]    findAll()
 * @method LegalEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalEntity::class);
    }

    //    /**
    //     * @return LegalEntity[] Returns an array of LegalEntity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function getActive(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.deregisteredAt IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }
}
