<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    # Configuration du DashBoard d'EasyAdmin
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sortir.com');
    }

    # Configuration des items du menu de l'interface EasyAdmin
    public function configureMenuItems(): iterable
    {
        # Menu Principal
        yield MenuItem::linktoDashboard ('Tableau de bord',           'fa fa-home');

        # Nouvel utilisateur
        yield MenuItem::linkToCrud      ('Gérer les Utilisateurs',    'fas fa-user',            Participant::class);

        # Nouvelle sortie
        yield MenuItem::linkToCrud      ('Gérer les Sorties',       'fas fa-beer',            Sortie::class);

        # Nouveau Lieu
        yield MenuItem::linkToCrud      ('Gérer les Lieux',          'fas fa-map-marked-alt',  Lieu::class);

        # Nouveau Site
        yield MenuItem::linkToCrud      ('Gérer les  Sites',          'fas fa-sitemap',         Site::class);

        # Nouvelle Ville
        yield MenuItem::linkToCrud      ('Gérer les Villes',        'fas fa-city',            Ville::class);
    }
}
