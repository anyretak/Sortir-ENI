<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Security\Core\Security;

class NewEventType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Event name:',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Event date and start time:',
                'widget' => 'single_text',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('limitDate', DateType::class, [
                'label' => 'Subscription due date:',
                'widget' => 'single_text',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('spots', NumberType::class, [
                'label' => 'Spots available:',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('duration', NumberType::class, [
                'label' => 'Duration (minutes):',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('description', TextAreaType::class, [
                'label' => 'Description:',
                'label_attr' => ['class' => 'app-event', 'id' => 'app-event-desc'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'required' => false,
                'label' => 'Campus:',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'required' => false,
                'label' => 'Location:',
                'label_attr' => ['class' => 'app-event'],
                'attr' => ['class' => 'app-event']
            ])

            ->add('user', EntityType::class, [
                'class' => User::class,
                'data' => $user,
                'attr' => ['style' => 'display:none']
            ]);

        $builder->add('city_details', CityType::class, ['mapped' => false, 'label' => false]);
        $builder->add('location_details', LocationType::class, ['mapped' => false, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
