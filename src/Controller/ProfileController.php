<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/participant/profil", name="app_profile")
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/participant/profil/{id}/show", name="app_profile_show")
     */
    public function showParticipant(Participant $participant): Response
    {

        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/participant/profil/add", name="app_profile_add")
     */
    public function addParticipant(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $participant = new Participant;

        $formParticipant = $this->createForm(ParticiPantType::class, $participant);

        $formParticipant->handleRequest($request);

        if ($formParticipant->isSubmitted() && $formParticipant->isValid()) {
            $participant->setRoles(['ROLE_USER']);
            // encode the plain password
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $participant->getPassword()
                )
            );
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/add.html.twig', [
            'formParticipant' => $formParticipant->createView()
        ]);
    }


    /**
     * @Route("/participant/profil/{id}/eddit", name="app_profile_eddit")
     */
    public function eddit(Participant $participant, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $formParticipant = $this->createForm(ParticipantType::class, $participant);
        $formParticipant->handleRequest($request);

        if ($formParticipant->isSubmitted() && $formParticipant->isValid()) {
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $participant->getPassword()
                )
            );
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/eddit.html.twig', [
            'participant' => $participant,
            'formParticipant' => $formParticipant->createView()
        ]);
    }

    /**
     * @Route("/participant/profil/{id}/delete", name="app_profile_delete")
     */
    public function deleteParticipant(Participant $participant, EntityManagerInterface $em): Response
    {
        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('app_profile');
    }
}
