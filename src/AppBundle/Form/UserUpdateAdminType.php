<?php
/**
 * Created by PhpStorm.
 * User: Julek
 * Date: 2018-12-02
 * Time: 17:55
 */

namespace AppBundle\Form;





use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserUpdateAdminType extends AbstractType
{
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
            ->add('password', TextType::class, array(
                'label' => 'Hasło',
            ))
            ->add('emailActivate', ChoiceType::class, array(
                'label' => 'Czy aktywny',
                'choices'  => array(
                    'TAK' => true,
                    'NIE' => false,
                ),
            ))
            ->add('activationToken', TextType::class, array(
                'label' => 'Token',
            ))
            ->add('role', ChoiceType::class, array(
                'label' => 'Uprawnienia',
                'choices'  => array(
                    'Admin' => 'admin',
                    'Moderator' => 'moderator',
                    'Supervisor' => 'supervisor',
                    'Client' => 'client',
                ),
            ))
        ->add('submit', SubmitType::class, array(
                'label' => 'Aktualizuj',
                'attr' => array('class' => 'btn btn-success pull-right')
            ));
    }
}