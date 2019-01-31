<?php

namespace App\Repository;

use App\Entity\CartesBancaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CartesBancaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartesBancaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartesBancaires[]    findAll()
 * @method CartesBancaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartesBancairesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CartesBancaires::class);
    }

    // /**
    //  * @return CartesBancaires[] Returns an array of CartesBancaires objects
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
    public function findOneBySomeField($value): ?CartesBancaires
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
