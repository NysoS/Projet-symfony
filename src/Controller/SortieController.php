<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\EditSortieType;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @Route("/showSortie/{id}", name="showSortie")
     */
    public function showSortie(Sorties $s, InscriptionsRepository $ir, ParticipantRepository $pr): Response
    {
        $inscriptions = $ir->findBy(array("sorties" => $s->getId()));
        $participants = $pr->findBy(array("id" => $inscriptions));

        return $this->render('sortie/showSortie.html.twig',[
            "sortie" => $s,
            "participants" => $participants
        ]);
    }

    /**
     * @Route("/editSortie/{id}", name="editSortie")
     */
    public function editSortie(Sorties $s, Request $req, VillesRepository $vr, LieuxRepository $lr, EntityManagerInterface $em): Response
    {
        $villes = $vr->findAll();
        $lieux = $lr->findAll();

        $form = $this->createForm(EditSortieType::class,$s);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute("addSortie");
        } else {
            return $this->render('sortie/editSortie.html.twig',[
                "form" => $form->createView(),
                "villes" => $villes,
                "lieux" => $lieux,
                "sortie" => $s
            ]);
        }
    }

    /**
     * @Route("/delSortie/{id}", name="delSortie")
     */
    public function delSortie(Sorties $s, EntityManagerInterface $em): Response
    {
        $em->remove($s);
        $em->flush();
        return $this->redirectToRoute("addSortie");
    }

    /**
     * @Route("/annuleSortie/{id}", name="annuleSortie")
     */
    public function annuleSortie(Sorties $s, Request $req, EntityManagerInterface $em, EtatsRepository $er): Response
    {
        $etat_annule = $er->findOneBy(array("id" => 6));

        $motif = "";
        if ($req->get("motif") != null) $motif = $req->get("motif");

        if ($motif == "") {
            return $this->render('sortie/annuleSortie.html.twig', [
                "sortie" => $s
            ]);
        } else {
            $s->setDescription("[ANNULEE motif : ".$motif."] ".$s->getDescription());
            $s->setEtats($etat_annule);
            $em->persist($s);
            $em->flush();
            //TODO rediriger vers accueil quand elle sera créée
            return $this->redirectToRoute("addSortie");
        }
    }
}
