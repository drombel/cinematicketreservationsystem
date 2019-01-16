<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CinemaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nazwa',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('street', TextType::class, array(
                'label' => 'Ulica',
                'attr' => array('pattern' => '[a-żA-Ż]*', 'class' => 'form-control'),
            ))
            ->add('streetNumber', TextType::class, array(
                'label' => 'Numer',
                'attr' => array('pattern' => '[0-9]*[ ]?[a-zA-Z]*', 'class' => 'form-control'),
            ))
            ->add('zipcode', TextType::class, array(
                'label' => 'Kod pocztowy',
                'attr' => array('pattern' => '[0-9]{2}\-[0-9]{3}', 'class' => 'form-control'),
            ))
            ->add('city', EntityType::class, array(
                'class' => 'AppBundle:City',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cinema'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cinema';
    }


}
