<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Cinema_hall_has_MovieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('movieId')
            ->add('cinemaHallId')
            ->add('timeStart', DateTimeType::class, array(
                'widget' => 'choice',
                'years' => array(2019,2020,2021,2022,2023,2024,2025),
                'label' => 'Data rozpoczęcia nadawania filmu w kinie'
            ))
            ->add('timeEnd', DateTimeType::class, array(
                'widget' => 'choice',
                'years' => array(2019,2020,2021,2022,2023,2024,2025),
                'label' => 'Data zakończenia nadawania filmu w kinie'
            ))
            ->add('timeMovieStart', TimeType::class, array(
                'input'  => 'datetime',
                'widget' => 'choice',
                'label' => 'Start filmu',
            ))
            ->add('timeMovieEnd', TimeType::class, array(
                'input'  => 'datetime',
                'widget' => 'choice',
                'label' => 'Koniec filmu',
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cinema_hall_has_Movie'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cinema_hall_has_movie';
    }


}
