<?php

namespace App\Repository;

use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

//    /**
//     * @return Inscription[] Returns an array of Inscription objects
//     */
//   
//   public function findInscrit($req)
//   {
//      
//       $testId = $req->get('inscrit');
//       
//       if ($testId != null){
//            // VIA SORTIE SELECT no_participant_id FROM sortie INNER JOIN inscription WHERE sortie.id = no_sortie_id
//           //  VIA INSCRIPTION SELECT * FROM inscription i INNER JOIN sortie ON sortie.id WHERE no_participant_id = 48 
//
//            $qb = $this->createQueryBuilder('i')
//            ->innerJoin('i.noSortie', 's')
//            ->addSelect('s')
//            ->andWhere('s.id = i.noSortie')
//            ->andWhere('i.noParticipant = :pa')
//            ->setParameter(':pa', $testId);   
//           //  ->where('i.noSortie = :sortie')
//           //  ->andWhere('i.noParticipant = :user')
//           //  
//           //  ->setParameter('user', $testId);
//           // $qb 
//               //  ->innerJoin('s.inscriptions', 'i')
//               //  ->addSelect('i')
//               //  ->andWhere('i.noSortie = s.id')
//               //  ->andWhere('i.noParticipant = :pa')
//               //  ->setParameter(':pa', $user);
//               return $qb  
//               ->getQuery()->getResult();
//       }
//       
//   }
    

   // 
   // public function roderBY($value): ?Inscription
   // {
   //     return $this->createQueryBuilder('i')
   //         ->andWhere('i.exampleField = :val')
   //         ->setParameter('val', $value)
   //         ->getQuery()
   //         ->getOneOrNullResult()
   //     ;
   // }
  

    // SELECT id FROM inscription 
    // WHERE no_sortie_id = $idSortie
    // AND no_participant_id = $idUser

    // /**
    //  * @return inscription par utilisateur
    //  */
    // public function getInscriptionByIdUserAndIdSortie($idSortie, $idUser)
    // {
    //     $entityManager = $this->getEntityManager();

    //     $query = $entityManager->createQuery(
    //         'SELECT i
    //         FROM App\Entity\Inscription i
    //         WHERE i.no_sortie_id = :sortie
    //         AND i.no_participant_id = :user'
    //     )->setParameter('sortie', $idSortie)
    //     ->setParameter('user', $idUser);

    //     return $query->getResult();
    // }

    /**
     * @return idInscription
     */
    public function findByIdUserAndIdSortie($idSortie, $idUser)
    {
    

          
        $qb = $this->createQueryBuilder('i')
            ->where('i.noSortie = :sortie')
            ->andWhere('i.noParticipant = :user')
            ->setParameter('sortie', $idSortie)
            ->setParameter('user', $idUser);


            return $qb  
            ->getQuery()->getResult();
        
    }    
 

}
