<?php

namespace App\Repository;

use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByExampleField($id, $containerName,$site,$dateEntre,$dateEt,$Organisateur,$finie, $inscrit  ,$nonInscrit)
    {
        $qb = $this->createQueryBuilder('s');
        if ($site != "moelleuxAuxPommes") {
            $qb ->andWhere('s.siteSortie = :sortie')
            ->setParameter('sortie', $site);   
        }
        if ($containerName != null){
            $qb ->andWhere("s.nom LIKE :nom")->setParameter("nom", "%$containerName%");
        }
        if ($dateEntre != null) { 
            $qb->andWhere("s.dateDebut >= :debut")->setParameter("debut", $dateEntre); 
        }
        if ($dateEt !=null) { 
            $qb->andWhere("s.dateDebut <= :fin")->setParameter("fin", $dateEt); 
        }
        if ($Organisateur != null) {
             $qb->andWhere("s.organisateur = :organisateur")->setParameter("organisateur", $id); 
            }
        if ($finie !=null) { 
            $qb ->innerJoin('s.noEtat', 'e')
                ->addSelect('e')
                ->andWhere('e.id = s.noEtat')
                ->andWhere('e.libelle = :el')
                ->setParameter("el", 'passee'); 
        }
        if ($inscrit != null){
                    //    VIA SORTIE SELECT no_participant_id FROM sortie INNER JOIN inscription WHERE sortie.id = no_sortie_id
                    //    VIA INSCRIPTION SELECT * FROM inscription i INNER JOIN sortie ON sortie.id WHERE no_participant_id = 48 
                    $test = 48;
                    $qb
                       ->innerJoin('s.inscriptions', 'i')
                       ->addSelect('i')
                      ->andWhere('i.noSortie = s.id')
                       ->andWhere('i.noParticipant = :pa')
                       ->setParameter(':pa', $inscrit);     
        }
        if ($nonInscrit != null){ 
        $qb
            ->innerJoin('s.inscriptions', 'i')
            ->addSelect('i')
            ->andWhere('i.noSortie = s.id')
            ->andWhere('i.noParticipant != :pa')
            ->setParameter(':pa', $nonInscrit);     
        }
            return $qb  
            ->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function Findinscrit($userID)
    {
        //    VIA SORTIE SELECT no_participant_id FROM sortie INNER JOIN inscription WHERE sortie.id = no_sortie_id
        //    VIA INSCRIPTION SELECT * FROM inscription i INNER JOIN sortie ON sortie.id WHERE no_participant_id = 48 
        $qb = $this->createQueryBuilder('s');
        $qb ->innerJoin('s.inscriptions', 'i')
            ->addSelect('i')
            ->andWhere('i.noSortie = s.id')
            ->andWhere('i.noParticipant = :pa')
            ->setParameter(':pa', $userID);     
            return $qb  
            ->getQuery()->getResult();
    
    }
}