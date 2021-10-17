<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegistrationFormType;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/profile/accueil", name="accueil")
     */
    public function index(ParticipantRepository $repoP, SortieRepository $repoS, SiteRepository $repoSite, Request $req, InscriptionRepository $repoI): Response
    {
        
        $userID = $this ->getUser()->getId();
        //Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
        $inscription = $repoI->findAll();
        $participant = $repoP->findAll();
        $user = $repoP-> find($userID);
        $site = $repoSite-> findAll();
        $containerName = $req->get('containerName');
        $site = $req->get('site');
        $dateEntre = $req->get('dateEntre');
        $dateEt = $req->get('dateEt');
        $Organisateur = $req->get('organisateur');
        $finie = $req->get('end');
        $inscrit = $req->get('inscrit');
        $nonInscrit = $req->get('noInscrit');
        $sortie = $repoS-> findAll();
        $sub = $req->get('sub');
        $dejaInscrit = $repoS->Findinscrit($userID);
        
        if($sub){
        $sortie = $repoS-> findByExampleField($userID, $containerName, $site, $dateEntre, $dateEt, $Organisateur, $finie, $inscrit, $nonInscrit);
        }
        return $this->render('accueil/index.html.twig', [
            'user' => $user,
            'sortie' => $sortie,
            'sites' => $site,
            'Inscriptions' => $inscription,
            'participant' => $participant,
            'dejaInscrit' => $dejaInscrit
        ]);
    }


    /**
     * @Route("/profile/accueil/listeProfils", name="listeProfils")
     */
    public function listeProfils(): Response
    {
        # Récupération de la liste des participants
        $participantRepo    =   $this               ->  getDoctrine()
                                                    ->  getRepository(Participant::class);
        $profils            =   $participantRepo    ->  findAll();

        # On envoi la liste des participants à la vue Twig
        return $this    ->  render('accueil/listeProfils.html.twig',[
            'profils'   => $profils
        ]);
    }


    /**
     * @Route("/profile/accueil/afficherProfil/{id}", name="afficherProfil", requirements={"id": "\d+" })
     */
    public function afficherProfil($id)
    {
        # Récupération de la liste des sites participants
        $siteRepo           =   $this               ->  getDoctrine()
                                                    ->  getRepository(Site::class);
        $sites              =   $siteRepo           ->  findAll();

        # Récupération du profil
        $participantRepo    =   $this               ->  getDoctrine()
                                                    ->  getRepository(Participant::class);
        $profil             =   $participantRepo    ->  find($id);

        # Création du formulaire de profil
        $profilForm         =   $this               ->  createForm(RegistrationFormType::class, $profil);

        return  $this       ->  render('accueil/profil.html.twig',[
            'profilForm'    =>  $profilForm         ->  createView(),
            'profil'        =>  $profil,
            'sites'         =>  $sites
        ]);
    }

    /**
     * @Route("/profile/accueil/modifierProfil/{id}", name="modifierProfil", requirements={"id": "\d+" })
     */
    public function modifierProfil( $id,
                                    Request $request,
                                    EntityManagerInterface $em,
                                    userPasswordHasherInterface $passwordHasher
                                    ):Response
    {
        # Récupération de la liste des sites participants
        $siteRepo           =   $this               ->  getDoctrine()
                                                    ->  getRepository(Site::class);
        $sites              =   $siteRepo           ->  findAll();

        # Récupération du profil à modifier
        $participantRepo    =   $this               ->  getDoctrine()
                                                    ->  getRepository(Participant::class);
        $profil             =   $participantRepo    ->  find($id);

        # Création du formulaire de présentation du profil
        $profilForm         =   $this               ->  createForm(RegistrationFormType::class,$profil);
        $profilForm         ->  remove('password');

        if ($profilForm ->  isSubmitted()  &&  $profilForm ->  isValid()) {
            # Récupération du nouveau mot de passe saisi
            $nouveauMDP     =   $profil                   ->  getNewPassword();
            if ($nouveauMDP) {
                # Hashage du nouveau MDP
                $hash       =   $passwordHasher     ->  hashPassword($profil, $nouveauMDP);
                $profil     ->  setNewPassword($hash);
            }

            $em->persist($profil);
            $em->flush();
            $this->addFlash('success', 'Modifications sauvegardées.');

            return $this->redirectToRoute('afficherProfil'.'/'.$id);
        }
        return  $this       ->  render('accueil/modifierProfil.html.twig',[
            'profilForm'    =>  $profilForm         ->  createView(),
            'profil'        =>  $profil,
            'sites'         =>  $sites
        ]);
    }

    /**
     * @Route("/profile/accueil/sauverProfil/{id}", name="sauverProfil", requirements={"id": "\d+" })
     */
    public function sauverProfil( $id,
                                  Request $request,
                                  EntityManagerInterface $em,
                                  userPasswordHasherInterface $passwordHasher):Response
    {
        # Récupération de la liste des sites participants
        $siteRepo           =   $this               ->  getDoctrine()
                                                    ->  getRepository(Site::class);
        $sites              =   $siteRepo           ->  findAll();

        # Récupération du profil à modifier
        $participantRepo    =   $this               ->  getDoctrine()
                                                    ->  getRepository(Participant::class);
        $profil             =   $participantRepo    ->  find($id);

        # Création du formulaire de modification
        $profilForm         =   $this               ->  createForm(RegistrationFormType::class,$profil);
        $profilForm         ->  remove('password')
                            ->  remove('imageFile');
        $profilForm         ->  handleRequest($request);

        if ($profilForm ->  isSubmitted()  &&  $profilForm ->  isValid()){
            # Récupération du nouveau mot de passe saisi
            $nouveauMDP     =   $profil             ->  getNewPassword();
            if($nouveauMDP){
                # Hashage du nouveau MDP
                $hash   =   $passwordHasher         ->  hashPassword($profil, $nouveauMDP);
                $profil ->  setNewPassword($hash);
            }

            $em             ->  persist($profil);
            $em             ->  flush();
            $this           ->  addFlash('success', 'Modifications sauvegardées.');

            return $this    ->  redirectToRoute('afficherProfil'.$id);
        }
        return  $this       ->  render('accueil/modifierProfil.html.twig',[
            'profilForm'    =>  $profilForm         ->  createView(),
            'profil'        =>  $profil,
            'sites'         =>  $sites
        ]);
    }

    /**
     * @Route("/profile/accueil/supprimerProfil/{id}", name="supprimerProfil", requirements={"id": "\d+" })
     */
    public function supprimerProfil($id, Request $request)
    {
        # Récupération du profil à supprimer
        $em         =   $this                       ->  getDoctrine()
                                                    ->  getManager();
        $profilRepo =   $em                         ->  getRepository(Participant::class);
        $profil     =   $profilRepo                 ->  find($id);

        # Supprssion de l'utilisateur
        $em         ->  remove($profil);
        $em         ->  flush();
        $this       ->  addFlash('success', 'L\'utilisateur à bien été supprimé.');
        return  $this   -> redirectToRoute('listeProfils');
    }
}
