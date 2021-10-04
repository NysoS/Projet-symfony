<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/participant/profil", name="app_profile")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    /**
     * @Route("/participant/profil/{id}/show", name="app_profile_show")
     */
    public function show(Participant $participant): Response
    {

        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/participant/profil/add", name="app_profile_add")
     */
    public function add( Request $request, EntityManager $em): Response
    {
        $participant = new Participant;

        $formParticipant = $this->createForm(ParticiPantType::class, $participant);
        $formParticipant->handleRequest($request);

        if($formParticipant->isSubmitted() && $formParticipant->isValid()){
            $em->persist($participant);
            $em->flush(); 

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
            'formParticipant'=> $formParticipant->createView()
        ]);
    }


     /**
     * @Route("/participant/profil/{id}/eddit", name="app_profile_eddit")
     */
    public function eddit(Participant $participant, Request $request, EntityManager $em): Response
    {

        $formParticipant = $this->createForm(ParticiPantType::class, $participant);
        $formParticipant->handleRequest($request);

        if($formParticipant->isSubmitted() && $formParticipant->isValid()){
            $em->persist($participant);
            $em->flush(); 

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
            'formParticipant'=> $formParticipant->createView()
        ]);
    }


}

