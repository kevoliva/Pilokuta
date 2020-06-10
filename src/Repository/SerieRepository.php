<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    // /**
    //  * @return Serie[] Returns an array of Serie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Serie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
      * @return Serie[] Returns an array of Serie objects
      */
    
    public function findSeriesByTournoi($idTournoi)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.tournoi = :idTournoi')
            ->setParameter('idTournoi', $idTournoi)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById($idTournoi): ?Serie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.tournoi = :val')
            ->setParameter('val', $idTournoi)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
