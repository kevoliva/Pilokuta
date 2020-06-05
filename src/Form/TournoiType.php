<?php

namespace App\Form;

use App\Entity\Serie;
use App\Entity\Tournoi;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut')
            ->add('dateFin')
            ->add('nbJoueursParEquipe')
            ->add('etat')
            ->add('libelle')
            ->add('series', EntityType::class, 
            [
                'class' => Serie::class,
                'choice_label' => function(Serie $serie)
                {
                  return $serie->getLibelle();  
                },
                    'multiple' => true,
                    'expanded' => true
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
