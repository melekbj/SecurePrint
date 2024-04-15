<?php

namespace App\Repository;

use App\Entity\FactureMateriel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FactureMateriel>
 *
 * @method FactureMateriel|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureMateriel|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureMateriel[]    findAll()
 * @method FactureMateriel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureMaterielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureMateriel::class);
    }

//    /**
//     * @return FactureMateriel[] Returns an array of FactureMateriel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FactureMateriel
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
