<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, MailerInterface $mailer): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # $user->setRoles(["ROLE_ADMIN"]);
          
            // encode the plain password
            $plainPassword = $form->get('password')->getData();
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            # création et envoi de l'email de bienvenue
            $email      =   new TemplatedEmail();
            $email      ->  to              ($user -> getMail())
                        ->  subject         ("Bienvenue sur Sortir.com")
                        ->  htmlTemplate    ('@email_templates/bienvenue.html.twig')
                        ->  context         ([
                                'prenom'        => $user -> getPrenom(),
                                'identifiant'   => $user -> getPseudo(),
                                'motDePasse'    => $plainPassword
                            ]) ;
            $mailer     ->  send($email);

            return $this->redirectToRoute('connexion');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
