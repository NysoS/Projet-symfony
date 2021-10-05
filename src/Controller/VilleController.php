<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Form\VilleType;
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
            $formVille = $this->createForm(VilleType::class);
            return $this->render('ville/index.html.twig', [
                'villes' => $villesRepository->findByFieldName($request->request->get('myWord')),
                'formVille' => $formVille->createView()
            ]);
        }
        return $this->redirectToRoute('app_ville');
    }

    /**
     * @Route("/ville/{id}/eddit", name="app_ville_eddit")
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
     * @Route("/ville/{id}/delete", name="app_ville_delete")
     */
    public function villeDelete(Villes $ville, EntityManagerInterface $em): Response
    {
        $em->remove($ville);
        $em->flush();

        return $this->redirectToRoute('app_ville');
    }
}