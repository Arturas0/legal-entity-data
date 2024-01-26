<?php

namespace App\Repository;

use App\Entity\LegalEntityStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LegalEntityStatus>
 *
 * @method LegalEntityStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalEntityStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalEntityStatus[]    findAll()
 * @method LegalEntityStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalEntityStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalEntityStatus::class);
    }

//    /**
//     * @return LegalEntityStatus[] Returns an array of LegalEntityStatus objects
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

//    public function findOneBySomeField($value): ?LegalEntityStatus
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
