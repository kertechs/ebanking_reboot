<?php

namespace App\Repository;

use App\Entity\Bankers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bankers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bankers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bankers[]    findAll()
 * @method Bankers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bankers::class);
    }

    // /**
    //  * @return Bankers[] Returns an array of Bankers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bankers
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
