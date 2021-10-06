<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Etats;
use App\Entity\Lieux;
use App\Entity\Sites;
use App\Entity\Villes;
use App\Entity\Sorties;
use App\Entity\Participant;
use App\Entity\Inscriptions;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        // création d'un site 
        for ($i = 0; $i < 5; $i++) {
            $site  = new Sites;
            $site->setNomSite($faker->words(2, true));
            $manager->persist($site);

            //création des utilisateurs 
            $participant = new Participant;
            $participant->setSites($site);
            $participant->setEmail($faker->email());
            $participant->setRoles([$faker->randomElement(['ROLE_ADMIN', 'ROLE_USER'])]);
            $participant->setNom($faker->lastName());
            $participant->setPrenom($faker->firstName());
            $participant->setActif($faker->randomElement([0, 1]));
            $participant->setTelephone($faker->phoneNumber());
            $participant->setPassword('$argon2id$v=19$m=65536,t=4,p=1$MGJVd21saldNOWxWdkhORg$mv+TGK3oanf+xyfVDptsDSWNudc7YFVEeD2fk4QBS00');

            $manager->persist($participant);


            //création d'un ville 
            $ville = new Villes;
            $ville->setNomVille($faker->city());
            $ville->setCodePostal($faker->randomNumber(5, true));
            $manager->persist($ville);


            //création d'un lieu
            $lieu = new Lieux;
            $lieu->setVilles($ville);
            $lieu->setNomLieu($faker->city());
            $lieu->setRue($faker->streetName());
            $lieu->setLongitude($faker->randomFloat());
            $lieu->setLatitude($faker->randomFloat());
            $manager->persist($lieu);

            //création d'un état
            $etat = new Etats;
            $etat->setLibelle($faker->randomElement(["Créée", "Ouverte", "Clôturée", "Activité en cours", "passée", "Annulé"]));
            $manager->persist($etat);

            //création d'une sortie
            $sortie = new Sorties;
            $sortie->setOrganisateur($participant);
            $sortie->setLieux($lieu);
            $sortie->setEtats($etat);
            $sortie->setDuree(5);
            $sortie->setNom($faker->word());
            $sortie->setDateDebut($faker->dateTimeBetween('-1 week', '+1 week'));
            $sortie->setDateCloture($faker->dateTimeBetween('+1 week', '+2 week'));
            $sortie->setNbInscriptionsMax(20);
            $sortie->setDescription($faker->paragraph());
            $sortie->setUrlPhoto("https://picsum.photos/seed/picsum/200/300");
            $manager->persist($sortie);

            for ($j = 0; $j < 5; $j++) {
                //participant random 
                $participant2 = new Participant;
                $participant2->setSites($site);
                $participant2->setEmail($faker->email());
                $participant2->setRoles(['ROLE_USER']);
                $participant2->setNom($faker->lastName());
                $participant2->setPrenom($faker->firstName());
                $participant2->setActif(true);
                $participant2->setTelephone($faker->phoneNumber());
                $participant2->setPassword('$argon2id$v=19$m=65536,t=4,p=1$MGJVd21saldNOWxWdkhORg$mv+TGK3oanf+xyfVDptsDSWNudc7YFVEeD2fk4QBS00');
                $manager->persist($participant2);

                //creation d'une inscription 
                $inscription = new Inscriptions;
                $inscription->setParticipants($participant2);
                $inscription->setSorties($sortie);
                $inscription->setDateInscription($faker->dateTime());
                $manager->persist($inscription);

                //ajout de l'inscription à la sortie 
                $sortie->addInscription($inscription);
            }
        }

        $manager->flush();
    }
}
