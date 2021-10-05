<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, ['label' => 'Email: '])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe: '])
            ->add('nom', null,  ['label' => 'Nom: '])
            ->add('prenom', null, ['label' => 'Prénom: '])
            ->add('telephone', null,  ['label' => 'Téléhpone: '])
            ->add('actif', ChoiceType::class, ['label' => 'Activé le compte utilisateur:', 'choices' => ['Activé le compte' => true, 'Désactivé le compte' => false]])
            ->add('sites', null, ['label' => 'Liste des sites :', 'choice_label' => 'nom_site']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
