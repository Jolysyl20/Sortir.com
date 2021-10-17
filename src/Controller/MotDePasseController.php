<?php

namespace App\Controller;

use App\Entity\ReinitMotDePasse;
use App\Repository\ParticipantRepository;
use App\Repository\ReinitMotDePasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MotDePasseController extends AbstractController
{
    /**
     * @Route("/reinit_mdp/{token}", name="reinit-mdp")
     */
    public function reinitialiserMotDePasse(RateLimiterFactory         $passwordRecoveryLimiter,
                                            string $token,
                                            Request $request,
                                            ReinitMotDePasseRepository $reinitMotDePasseRepository,
                                            EntityManagerInterface     $em,
                                            UserPasswordHasherInterface $userPasswordHasher): Response
    {
        # Limitation du nombre de rafraichissement de la page
        $limiter    =   $passwordRecoveryLimiter -> create($request -> getClientIp());
        if (false === $limiter -> consume(1) -> isAccepted()){
            $this   ->  addFlash    ('danger', 'Votre compte est désactivé pour 2 minutes. Raison : Trop de tentatives infructueuses.');
            return  $this   ->  redirectToRoute('accueil');
        }

        $reinitMDP  =   $reinitMotDePasseRepository  -> findOneBy(['token' => sha1($token)]);
        # Si le token est inexistant ou  si expiré, on le supprime
        if(!$reinitMDP || $reinitMDP -> getDateExpiration() < new \DateTime('now')){
            if($reinitMDP){
                $em     -> remove   ($reinitMDP);
                $em     -> flush    ();
            }
            $this   -> addFlash ('danger', 'Votre demande est expirée, veuillez refaire une demande.');
            return $this -> redirectToRoute('accueil');
        }

        # Création du formulaire de changement de mot de passe
        $formMDP    =   $this   ->  createFormBuilder()
                    ->  add('motDePasse', PasswordType::class, [
                            'label'         =>  'Nouveau mot de passe',
                            'constraints'   =>  [
                            new Length([
                                'min'           =>  6,
                                'minMessage'    =>  'Le mot de passe doit faire au moins 6 caractères.'
                            ]),
                            new NotBlank([
                                'message'   =>  'Veuillez renseigner un nouveau mot de passe.'
                            ])
                        ]
                    ])
                    -> getForm();

        # Gestion de la soumission du formulaire
        $formMDP    ->  handleRequest($request);
        if($formMDP -> isSubmitted()    &&  $formMDP -> isValid()){
            $mdp = $formMDP ->  get         ('motDePasse')
                            ->  getData     ();
            $participant    =   $reinitMDP          ->  getParticipant  ();
            $hash           =   $userPasswordHasher ->  hashPassword    ($participant, $mdp);
            $participant    ->  setPassword ($hash);
            $em             ->  remove      ($reinitMDP);
            $em             ->  flush       ();
            $this           ->  addFlash    ('success','Votre mot de passe a été modifié.');
            return $this    ->  redirectToRoute('accueil');
        }

        return $this    ->  render('security/reinitMDPForm.html.twig', [
            'form'  =>  $formMDP    -> createView()
        ]);
    }


    /**
     * @Route("/mot_de_passe_oublie", name="mot-de-passe-oublie")
     */
    public function motDePasseOublie(RateLimiterFactory         $passwordRecoveryLimiter,
                                     MailerInterface            $mailer,
                                     Request                    $request,
                                     ParticipantRepository      $participantRepository,
                                     ReinitMotDePasseRepository $reinitMotDePasseRepository,
                                     EntityManagerInterface     $em): Response
    {
        # Création d'une clé qui permet d'identifier un utilisateur en particulier
        $limiter    =   $passwordRecoveryLimiter -> create($request -> getClientIp());
        if (false === $limiter -> consume(1) -> isAccepted()){
            $this   ->  addFlash    ('danger', 'Trop de tentatives de connexion. Merci de réessayer dans 1 minute.');
            return  $this   ->  redirectToRoute('accueil');
        }

        # Création du formulaire (pas de lien avec une entité) de réinitialisation de mdp.
        $emailForm = $this  ->  createFormBuilder()
                            ->  add('email', EmailType::class, [
                                        'constraints' =>    [
                                            new NotBlank([
                                                'message' => 'Veuillez renseigner votre email'
                                            ])
                                        ]
                                    ])
                            -> getForm();

        # Traitement du contenu du formulaire.
        $emailForm  ->  handleRequest($request);
        if($emailForm   ->  isSubmitted()   &&  $emailForm ->  isValid()){

            # On vérifie que l'on a un utilisateur qui correspond à l'email saisi.
            # Récupération de l'email saisi :
            $emailVal = $emailForm  ->  get     ('email')
                                    ->  getData ();

            # On chercher notre utilisateur dans la BDD:
            $participant = $participantRepository  ->  findOneBy(['mail' => $emailVal]);


            # On vérifie que l'on a bien un utilisateur qui correspond:
            if($participant){
                # On vérifie la présence d'un token déja existant
                $ancienReinitMDP    =   $reinitMotDePasseRepository ->  findOneBy(['participant' => $participant]);
                if($ancienReinitMDP){
                    # Si il y a un token on le supprime
                    $em     ->  remove  ($ancienReinitMDP);
                    $em     ->  flush   ();
                }
                # Si il y a un participant, on créé un token
                $reinitMDP  =   new ReinitMotDePasse();
                $reinitMDP  ->  setParticipant($participant)
                            ->  setDateExpiration(new \DateTime('+ 2hours'));

                # Génération de la chaine de caractères (20 caractères) correspondant au token
                # Suppression des caractères qui peuvent casser l'url avec la méthode str_replace()
                # Utilisation de base64_encode pour récupérer une chaine exploitable dans une url
                $token      =   substr      (str_replace(['+', '/','='],'', base64_encode(random_bytes(30))), 0, 20);
                $reinitMDP  ->  setToken    (sha1($token));
                $em         ->  persist     ($reinitMDP);
                $em         ->  flush       ();

                # Envoi du mail de réinitialisation
                $email      =   new TemplatedEmail();
                $email      ->  to              ($emailVal)
                            ->  subject         ('Demande de réinitialisation de mot de passe')
                            ->  htmlTemplate    ('@email_templates/reinitMDP.html.twig')
                            ->  context         ([
                                'pseudo'   =>  $participant   ->  getPseudo(),
                                'token'    =>  $token
                            ]);
                $mailer     ->  send($email);
            }
            $this           ->  addFlash('success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe');
            return $this    ->  redirectToRoute('accueil');
        }

        return $this    ->  render('security/mdpOublie.html.twig', [
            'form'  =>  $emailForm  -> createView()
        ]);
    }
}
