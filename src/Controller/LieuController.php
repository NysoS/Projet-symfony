<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Entity\Villes;
use App\Repository\LieuxRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LieuController extends AbstractController
{

    /**
     * @Route("/lieux_ville/{ville}", name="lieux_ville", methods={"GET"})
     */
    public function lieux_ville(Villes $ville, VillesRepository $vr, LieuxRepository $lr, NormalizerInterface $ni): Response
    {
        
        $ville = $vr->findOneBy(["id" => $ville->getId()]);
        $lieux = $lr->findBy(["villes" => $ville]);
        $normalize = $ni->normalize($lieux, null, ["groups" => "lieu:read"]);
        return $this->json($normalize);
    }

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
