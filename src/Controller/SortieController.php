<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/addSortie", name="addSortie")
     */
    public function index(): Response
    {
        return $this->render('sortie/addSortie.html.twig');
    }
}
