<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Imię',
                'attr' => array('class' => 'form-control', 'autofocus' => true),
            ))
            ->add('surname', TextType::class, array(
                'label' => 'Nazwisko',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Podane hasła są różne',
                'first_options'  => array(
                    'label' => 'Hasło',
                    'attr' => array('class' => 'form-control'),
                ),
                'second_options' => array(
                    'label' => 'Powtórz hasło',
                    'attr' => array('class' => 'form-control'),
                ),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Zarejestruj',
                'attr' => array('class' => 'btn btn-success pull-right')
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
