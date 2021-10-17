<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/profile/nouvelle-sortie", name="nouvelle_sortie")
     */
    public function nouvelleSortie(Request $request, EntityManagerInterface $em, EtatRepository $reposEtat, LieuRepository $repoL): Response
    {
        $sortie = new Sortie();
        $Etat = $reposEtat->find(1);

        // récupérer le site organisateur
        $idUser = $this->getUser()->getId();
        $lieu = $repoL->findAll();
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $repoParticipant->find($idUser);
        $sortie->setOrganisateur($participant);
        $idSite = $participant->getSiteParticipant();
        $sortie->setSiteSortie($idSite);

        //je crée un formulaire
        $formSortie = $this->createForm(SortieType::class, $sortie);
        //j'hydrate $sortie
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            //etat par défaut 'créée'
            $sortie->setNoEtat($Etat);
            $sortie->setSiteSortie($idSite);
            $sortie->setNoVille($sortie->getNoLieu()->getVillesNoVille());

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Votre sortie à bien été créée !');

            return $this->render('sortie/nouvelleSortie.html.twig', [
                "formSortie" => $formSortie->createView(),
                "sortie" => $sortie,
            ]);
        }

        return $this->render('sortie/nouvelleSortie.html.twig', [
            'formSortie' => $formSortie->createView(),
            'sortie' => $sortie,
            'Lieu' => $lieu
        ]);
    }

    /**
     * @Route("/profile/modifier-sortie/{id}", name="modifier_sortie", requirements={"id": "\d+" });
     */
    public function modifierSortie($id, EntityManagerInterface $em, Request $request)
    {
        //Modification d'une sortie :
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        $idUser = $this->getUser()->getId();
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $repoParticipant->find($idUser);
        $sortie->setOrganisateur($participant);
        $idSite = $participant->getSiteParticipant();
        $sortie->setSiteSortie($idSite);

        //création du formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setSiteSortie($idSite);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Votre sortie à bien été modifiée !');
            return $this->redirectToRoute('accueil');
        }
        return $this->render('sortie/modifierSortie.html.twig', [
            "sortieForm" => $sortieForm->createView(),
            "sortie" => $sortie,
        ]);
    }

    /**
     * @Route("/profile/afficher-sortie/{id}", name="afficher_sortie", requirements={"id": "\d+" });
     */
    public function afficherSortie($id, SortieRepository $sortieRepo)
    {
        $userID = $this->getUser()->getId();
        //récupération de la sortiesortie :
        $sortie = $sortieRepo->find($id);
        //création du formulaire sortie
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        //récupération des inscriptions du user
        $dejaInscrit = $sortieRepo->Findinscrit($userID);
        //récupération des inscrits sur cette sortie
        $inscripRepo = $this->getDoctrine()->getRepository(Inscription::class);
        $inscrip = $inscripRepo->findBy(array('noSortie' => $id));
        // message exception
        if (empty($sortie)) {
            throw $this->createNotFoundException("la sortie n'existe pas");
        }
        // passage à la vue
        return $this->render('sortie/afficherSortie.html.twig', [
            "sortieForm" => $sortieForm->createView(),
            "sortie" => $sortie,
            "inscrip" => $inscrip,
            "dejaInscrit" => $dejaInscrit,
        ]);
    }

    /**
     * @Route("/profile/supprimer-sortie/{id}", name="supprimer_sortie", requirements={"id": "\d+"})
     */
    public function supprimerSortie($id, EntityManagerInterface $em)
    {
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        //on supprime la sortie
        $em->remove($sortie);
        $em->flush();
        $this->addFlash('success', "La sortie est supprimée");
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/profile/publier-sortie/{id}", name="publier_sortie", requirements={"id": "\d+" });
     */
    public function publierSortie($id, EntityManagerInterface $em, EtatRepository $reposEtat)
    {
        //Modification d'une sortie :
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        $Etat = $reposEtat->find(5); 
        $sortie->setNoEtat($Etat);

        $em->persist($sortie);
        $em->flush();

        $this->addFlash('success', 'Votre sortie a bien été publiée !');
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/profile/inscription-sortie/{id}", name="inscription_sortie", requirements={"id": "\d+" });
     */
    public function inscriptionSortie($id, EntityManagerInterface $em)
    {
        //récupération du participant :
        $participant = $this->getUser();
        //récupération de la sortie :
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        //création d'une inscription
        $inscription = new Inscription();
        $inscription->setDateInscription(new DateTime());
        $inscription->setNoParticipant($participant);
        $inscription->setNoSortie($sortie);
        //sauvegarde de l'inscription
        $em->persist($inscription);
        $em->flush();

        $this->addFlash('success', 'Votre inscription a bien été enregistrée !');
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/profile/desister/{id}", name="desister", requirements={"id": "\d+" });
     */
    public function desister($id, EntityManagerInterface $em, InscriptionRepository $Ir)
    {
        //récupération du participant :
        $participant = $this->getUser();
        $idParticipant = $participant->getId();

        //récupération de l'inscription :
        $inscription = $Ir->findByIdUserAndIdSortie($id, $idParticipant);

        //on supprime l'inscription
        $em->remove($inscription[0]);
        $em->flush();

        $this->addFlash('success', 'Vous êtes désinscrit !');
        return $this->redirectToRoute('accueil');
    }


    /**
     * @Route("/profile/annuler-sortie/{id}", name="annuler_sortie", requirements={"id": "\d+" });
     */
    public function annulerSortie($id, Request $request, EntityManagerInterface $em, EtatRepository $reposEtat)
    {
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        if ($request->isMethod('post')) {
            $Etat = $reposEtat->find(4);
            $sortie->setNoEtat($Etat);

            $motif = $request->request->get('_inputMotif');
            $sortie->setMotif($motif);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a bien été annulée !');
            return $this->redirectToRoute('accueil');
        }
        return $this->render('sortie/annulerSortie.html.twig', [
            "sortie" => $sortie,
        ]);
    }
}
