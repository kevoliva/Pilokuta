<?php

namespace App\Form;

use App\Entity\Poule;
use App\Entity\Serie;
use App\Entity\Tournoi;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut',DateType::class, [
                'help' => '10/08/2000',])
            ->add('dateFin',DateType::class, [
                'help' => '11/06/2001',])
            ->add('etat',TextType::class, [
                'help' => 'en cours',])
            ->add('libelle',TextType::class, [
                'help' => 'Tournoi 1vs1',])
            ->add('series', CollectionType::class, [
                'entry_type' => SerieType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
