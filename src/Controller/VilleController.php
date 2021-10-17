<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/profile/creer-ville", name="creer_ville")
     */
    public function creerVille(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $formVille = $this->createForm(VilleType::class, $ville);
        $formVille->handleRequest($request);

        if($formVille->isSubmitted() && $formVille->isValid()) {
            $em->persist($ville);
            $em->flush();
            $this -> addFlash('success', 'Ville créée !');

            return $this->redirectToRoute('nouvelle_sortie');
        }    

        return $this->render('ville/creerVille.html.twig', [
            'formVille' => $formVille->createView(),
        ]);
    }

    /**
     * @Route("/admin/modifier-villes/{id}", name="modifier_villes", requirements={"id": "\d+" });
     */
    public function modifierVilles($id, EntityManagerInterface $em, Request $request)
    {
        //Modification d'une ville :
        $villeRepo = $this->getDoctrine()->getRepository(Ville::class);
        $ville = $villeRepo->find($id);
        //création du formulaire
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $em->persist($ville);
            $em->flush();
            $this->addFlash('success', 'Votre ville à bien été modifiée !');
            return $this->redirectToRoute('accueil');
        }
        return $this->render('ville/modifierVille.html.twig', [
            "villeForm" => $villeForm->createView(),
            "ville" => $ville,
        ]);
    }


}
