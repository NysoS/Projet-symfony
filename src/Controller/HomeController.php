<?php

namespace App\Controller;

use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SortiesRepository $sortie, SitesRepository $sites): Response
    {
        $lstSortie = $sortie->findAll();
        $lstSites = $sites->findAll();

        return $this->render('home/index.html.twig', [
            'lstSortie' => $lstSortie,
            'lstSites' => $lstSites,
        ]);
    }
}
