<?php

namespace App\Repository;

use App\Entity\Poule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Poule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poule[]    findAll()
 * @method Poule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PouleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poule::class);
    }

    // /**
    //  * @return Poule[] Returns an array of Poule objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Poule
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
      * @return Poule[] Returns an array of Poule objects
      */
    
    public function findPoulesBySerie($idSerie)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.serie = :idSerie')
            ->setParameter('idSerie', $idSerie)
            ->getQuery()
            ->getResult()
        ;
    }
    
}
