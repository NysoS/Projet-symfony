<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Form\VilleType;
use App\Repository\LieuxRepository;
use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VilleController extends AbstractController
{

    /**
     * @Route("/ville", name="app_ville")
     */
    public function index(VillesRepository $villesRepository, Request $request, EntityManagerInterface $em): Response
    {

        $ville = new Villes;
        $formVille = $this->createForm(VilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {
            $em->persist($ville);
            $em->flush();
            // je signale l'utilisateur 
            $this->addFlash('success', ' de la ville ok');
            $this->redirectToRoute('app_ville');
        }

        return $this->render('ville/index.html.twig', [
            'villes' => $villesRepository->findAll(),
            'formVille' => $formVille->createView()
        ]);
    }

    /**
     * @Route("/ville/find", name="app_ville_find")
     */
    public function villeFind(Request $request, VillesRepository $villesRepository): Response
    {
        if ($request->request->get('myWord')) {

            // $formVille = $this->createForm(VilleType::class);
            return $this->render('ville/index.html.twig', [
                'villes' => $villesRepository->findByFieldName($request->request->get('myWord')),
                //'formVille' => $formVille->createView()
            ]);
        }
        return $this->redirectToRoute('app_ville');
    }

    /**
     * @Route("/admin/ville/{id}/eddit", name="app_ville_eddit")
     */
    public function villeEddit(Villes $ville, Request $request, EntityManagerInterface $em): Response
    {

        $formVille = $this->createForm(VilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {
            $em->persist($ville);
            $em->flush();
            return $this->redirectToRoute('app_ville');
        };

        return $this->render('ville/eddit.html.twig', [
            'formVille' => $formVille->createView(),
            'ville' => $ville
        ]);
    }


    /**
     * @Route("/admin/ville/{id}/delete", name="app_ville_delete")
     */
    public function villeDelete(Villes $ville, EntityManagerInterface $em, LieuxRepository $lieuxRepository, SortiesRepository $sortiesRepository): Response
    {
        //rechercher les lieux dans la ville  
        $allLieux = $lieuxRepository->findBy(['villes' => $ville->getId()]);

        //supprimer les lieux dans la ville  
        foreach ($allLieux as $lieu) {
            $allSorties = $sortiesRepository->findBy(['lieux' => $lieu->getId()]);
            foreach ($allSorties as $sortie) {
                $sortie->setLieux(NULL);
            }
            $em->remove($lieu);
        }

        $em->remove($ville);
        $em->flush();

        // je signale l'utilisateur 
        $this->addFlash('success', 'suppression de la ville ok');

        return $this->redirectToRoute('app_ville');
    }
}
