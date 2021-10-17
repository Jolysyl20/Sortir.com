<?php

namespace App\Repository;

use App\Entity\ReinitMotDePasse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReinitMotDePasse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReinitMotDePasse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReinitMotDePasse[]    findAll()
 * @method ReinitMotDePasse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReinitMotDePasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReinitMotDePasse::class);
    }

    // /**
    //  * @return ReinitMotDePasse[] Returns an array of ReinitMotDePasse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReinitMotDePasse
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
