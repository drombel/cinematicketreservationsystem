<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tytuł',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Opis',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('price', MoneyType::class, array(
                'label' => 'Cena',
                'currency' => 'PLN',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('poster', FileType::class, array(
                'label' => 'Plakat'
            ))
            ->add('scene', FileType::class, array(
                'label' => 'Scena z filmu'
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Movie'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_movie';
    }


}
