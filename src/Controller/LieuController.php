<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/profile/creer-lieu", name="creer_lieu")
     */
    public function creerLieu(Request $request, EntityManagerInterface $em): Response
    {
        $lieu = new Lieu;
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if($formLieu->isSubmitted() && $formLieu->isValid()) {
            $em->persist($lieu);
            $em->flush();
            $this -> addFlash('success', 'Lieu créé !');

            return $this->redirectToRoute('nouvelle_sortie');
        }    

        return $this->render('lieu/creerLieu.html.twig', [
            'formLieu' => $formLieu->createView(),
        ]);
    }

   /**
     * Permet d'actualiser les lieux
     *
     * @Route("/refreshLieux", name="refresh_lieux")
     * @return Response
     */
    public function refreshLieux(LieuRepository $lieuRepo): Response
    {
        $lieux = $lieuRepo->find(1);
       
        return $this->json($lieux);
    }


}
