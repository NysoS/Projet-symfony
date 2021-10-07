<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\EditSortieType;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SitesRepository;
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
    public function addSortie(Request $req, VillesRepository $vr, LieuxRepository $lr, EtatsRepository $er, SitesRepository $sr, EntityManagerInterface $em): Response
    {
        $sortie = new Sorties();
        $villes = $vr->findAll();

        $form = $this->createForm(SortiesType::class,$sortie);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $sortie->setNom($req->get("nom"));
            $sortie->setDateDebut(new \DateTime($req->get("date_debut")));
            $sortie->setDateCloture(new \DateTime($req->get("date_cloture")));
            $sortie->setNbInscriptionsMax($req->get("nbInscriptionsMax"));
            $sortie->setDuree($req->get("duree"));
            $sortie->setDescription($req->get("description"));
            $sortie->setOrganisateur($this->getUser());
            $sortie->setSite($sr->findOneBy(array("id" => $this->getUser()->getSites()->getId())));
            if ($req->get("lieu") != null) $sortie->setLieux($lr->findOneBy(array("id" => str_replace("lieu","",$req->get("lieu")))));
            if ($req->get("ville") != null) $sortie->getLieux()->setVilles($vr->findOneBy(array("id" => str_replace("ville","",$req->get("ville")))));

            if ($req->get("etat") == "enregistrer") $sortie->setEtats($er->findOneBy(array("libelle" => "Créée")));
            else $sortie->setEtats($er->findOneBy(array("libelle" => "Ouverte")));

            $inscription = new Inscriptions();
            $inscription->setSorties($sortie);
            $inscription->setParticipants($this->getUser());
            $inscription->setDateInscription(new \DateTime(date("Y-m-d H:i:s")));

            $em->persist($sortie);
            $em->persist($inscription);
            $em->flush();
            return $this->redirectToRoute("home");
        } else {
            return $this->render('sortie/addSortie.html.twig',[
                "formSortie" => $form->createView(),
                "villes" => $villes
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
            $s->setDateDebut($req->get("date_debut"));
            $s->setDateCloture($req->get("date_cloture"));
            $s->setNbInscriptionsMax($req->get("nbInscriptionsMax"));
            $s->getDuree($req->get("duree"));
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute("home");
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
        return $this->redirectToRoute("home");
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
            return $this->redirectToRoute("home");
        }
    }

    /**
     * @Route("/desistSortie/{sortie}/{user}", name="desistSortie")
     */
    public function desistSortie(Sorties $sortie, Participant $user, EntityManagerInterface $em, InscriptionsRepository $ir): Response
    {
        $inscription = $ir->findOneBy(["sorties" => $sortie->getId(),"participants" => $user->getId()]);
        $em->remove($inscription);
        $em->flush();
        return $this->redirectToRoute("home");
    }
}
