<?php

namespace App\Repository;

use App\Entity\DeviMateriel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeviMateriel>
 *
 * @method DeviMateriel|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviMateriel|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviMateriel[]    findAll()
 * @method DeviMateriel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviMaterielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviMateriel::class);
    }

//    /**
//     * @return DeviMateriel[] Returns an array of DeviMateriel objects
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

//    public function findOneBySomeField($value): ?DeviMateriel
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
