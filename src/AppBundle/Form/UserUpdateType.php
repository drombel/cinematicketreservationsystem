<?php
/**
 * Created by PhpStorm.
 * User: Julek
 * Date: 2018-12-02
 * Time: 17:55
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'Nowy e-mail',
                'required' => false,
                'attr' => array('class' => 'form-control'),
            ))
            ->add('oldPassword', PasswordType::class, array(
                'label' => 'Stare hasło',
                'required' => false,
                'attr' => array('class' => 'form-control'),
            ))
            ->add('newPassword', PasswordType::class, array(
                'label' => 'Nowe hasło',
                'required' => false,
                'attr' => array('class' => 'form-control'),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Aktualizuj',
                'attr' => array('class' => 'btn btn-success pull-right')
            ));
    }
}