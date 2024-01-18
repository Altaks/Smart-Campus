<?php

namespace App\Repository;

use App\Entity\DemandeTravaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeTravaux>
 *
 * @method DemandeTravaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeTravaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeTravaux[]    findAll()
 * @method DemandeTravaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeTravauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeTravaux::class);
    }

//    /**
//     * @return DemandeTravaux[] Returns an array of DemandeTravaux objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DemandeTravaux
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
