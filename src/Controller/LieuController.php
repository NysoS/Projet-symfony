<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/quickAddLieu", name="quickAddLieu")
     */
    public function quickAddLieu(Request $req, EntityManagerInterface $em, VillesRepository $vr): Response
    {
        $ville = $vr->findOneBy(array("id" => $req->get("ville")));

        $lieu = new Lieux();
        $lieu->setVilles($ville);
        $lieu->setNomLieu($req->get("nom_lieu"));
        $lieu->setRue($req->get("rue"));

        $em->persist($lieu);
        $em->flush();
        return $this->redirectToRoute("addSortie");
    }
}
