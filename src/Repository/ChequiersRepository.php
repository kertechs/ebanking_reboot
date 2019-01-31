<?php

namespace App\Repository;

use App\Entity\Chequiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Chequiers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chequiers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chequiers[]    findAll()
 * @method Chequiers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChequiersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chequiers::class);
    }

    // /**
    //  * @return Chequiers[] Returns an array of Chequiers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chequiers
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
