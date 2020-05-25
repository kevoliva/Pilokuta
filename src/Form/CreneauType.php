<?php

namespace App\Form;

use App\Entity\Creneau;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use App\Entity\Partie;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CreneauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('laDate')
            ->add('heureDebut')
            ->add('duree')
            ->add('disponibilite')
            ->add('user', EntityType::class,[
                'class' => User::class,
                'choice_label' => function(User $user){
                    return $user->getNom();
                  },
                  'multiple' => true
            ])
            ->add('tournoi', EntityType::class, [
                'class' => Tournoi::class,
                'choice_label' => function(Tournoi $tournoi){
                    return $tournoi->getLibelle();
                  },
                  'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Creneau::class,
        ]);
    }
}