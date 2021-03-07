<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  TextType::class, [
                'label' => 'Location name:',
                'label_attr'=> ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('city',  EntityType::class, [
                'class' => City::class,
                'required' => false,
                'label' => 'City:',
                'label_attr'=> ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('street',  TextType::class, [
                'label' => 'Street:',
                'label_attr'=> ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('latitude', IntegerType::class, [
                'label' => 'Latitude:',
                'label_attr'=> ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('longitude', IntegerType::class, [
                'label' => 'Longitude:',
                'label_attr'=> ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
