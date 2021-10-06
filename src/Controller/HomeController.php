<?php

namespace App\Controller;

use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SortiesRepository $sortie): Response
    {
        $lstSortie = $sortie->findAll();

        return $this->render('home/index.html.twig', [
            'lstSortie' => $lstSortie
        ]);
    }
}
