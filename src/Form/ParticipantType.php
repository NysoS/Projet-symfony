<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',null,['label'=>'Email: '])
            ->add('roles', null,['label'=>'Email: '] )
            ->add('password', null, ['label'=>'Mot de passe: '])
            ->add('nom', null,  ['label'=>'Nom: '])
            ->add('prenom',null, ['label'=>'Prénom: '])
            ->add('telephone', null,  ['label'=>'Téléhpone: '])
            ->add('actif', null, ['label'=>'Activé le compte utilisateur:'])
            ->add('sites', null, ['label'=>'Les sites:'] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
