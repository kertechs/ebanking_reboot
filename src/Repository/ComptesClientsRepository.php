<?php

namespace App\Repository;

use App\Entity\ComptesClients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ComptesClients|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComptesClients|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComptesClients[]    findAll()
 * @method ComptesClients[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComptesClientsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ComptesClients::class);
    }

    // /**
    //  * @return ComptesClients[] Returns an array of ComptesClients objects
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
    public function findOneBySomeField($value): ?ComptesClients
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
