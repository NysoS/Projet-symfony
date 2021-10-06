<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

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
     * @Route("/participant/profil/{id}/status", name="app_profile_status")
     */
    public function statusParticipant(Participant $participant, EntityManagerInterface $em): Response
    {
        $etat = !$participant->getActif();
        $participant->setActif($etat);

        $em->flush();

        return $this->redirectToRoute('app_profile');
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

    /**
     * @Route("/participant/profil/addJson", name="app_profile_add_json")
     */
    public function addJsonParticipant(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, SitesRepository $sitesRepository)
    {
        if ($request->getMethod() == "POST") {

            try {
                $files = $request->files->all();


                foreach ($files as $file) {

                    dd($file->getMimetype());
                    //vérification de l'extension de mon ficher
                    $nomFichier = $file->getClientOriginalName();

                    //vérification si  .json ou .csv 
                    if (str_contains($nomFichier, '.csv') && $file->getMimetype() == "text/plain") {
                        dd('il y a du csv');
                        //traitement dans le cas ou c'est du csv 


                    } elseif (str_contains($nomFichier, '.json')) {
                        dd('il y a du csv');
                        //traitement dans le cas ou c'est du json 

                        $json = file_get_contents($file->getPathname());
                        $siteId = json_decode($json)->sites;
                        $site = $sitesRepository->findOneBy(['id' => $siteId]);
                        $participant = $serializer->deserialize($json, Participant::class, 'json');
                        $participant->setSites($site);
                        $em->persist($participant);
                        $em->flush();
                    }

                    return $this->redirectToRoute('app_profile_add_json');
                }
            } catch (NotEncodableValueException $e) {
                return $this->render('profile/test.html.twig');
            }
        }

        return $this->render('profile/test.html.twig');
    }
}
