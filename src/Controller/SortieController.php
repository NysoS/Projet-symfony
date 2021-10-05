<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\LieuxRepository;
use App\Repository\VillesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/addSortie", name="addSortie")
     */
    public function addSortie(Request $req, VillesRepository $vr, LieuxRepository $lr): Response
    {
        $sortie = new Sorties();
        $villes = $vr->findAll();
        $lieux = $lr->findAll();

        $form = $this->createForm(SortiesType::class,$sortie);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {

        } else {
            return $this->render('sortie/addSortie.html.twig',[
                "formSortie" => $form->createView(),
                "villes" => $villes,
                "lieux" => $lieux
            ]);
        }
    }
}
