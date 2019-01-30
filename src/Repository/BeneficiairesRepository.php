<?php

namespace App\Repository;

use App\Entity\Beneficiaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Beneficiaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beneficiaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beneficiaires[]    findAll()
 * @method Beneficiaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeneficiairesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Beneficiaires::class);
    }

    // /**
    //  * @return Beneficiaires[] Returns an array of Beneficiaires objects
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
    public function findOneBySomeField($value): ?Beneficiaires
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
