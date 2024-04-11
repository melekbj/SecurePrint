<?php

namespace App\Repository;

use App\Entity\CommandeMateriel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandeMateriel>
 *
 * @method CommandeMateriel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeMateriel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeMateriel[]    findAll()
 * @method CommandeMateriel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeMaterielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeMateriel::class);
    }

//    /**
//     * @return CommandeMateriel[] Returns an array of CommandeMateriel objects
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

//    public function findOneBySomeField($value): ?CommandeMateriel
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
