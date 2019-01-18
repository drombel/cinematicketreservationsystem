<?php
/**
 * Created by PhpStorm.
 * User: Julek
 * Date: 2019-01-03
 * Time: 20:43
 */

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserUpdateModeratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Imię',
                'attr' => array('class' => 'form-control', 'autofocus' => true),
                'disabled' => true,
            ))
            ->add('surname', TextType::class, array(
                'label' => 'Nazwisko',
                'attr' => array('class' => 'form-control'),
                'disabled' => true,
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail',
                'attr' => array('class' => 'form-control'),
                'disabled' => true,
            ))
            ->add('password', TextType::class, array(
                'label' => 'Hasło',
                'disabled' => true,
            ))
            ->add('emailActivate', ChoiceType::class, array(
                'label' => 'Czy aktywny',
                'choices'  => array(
                    'TAK' => true,
                    'NIE' => false,
                ),
                'disabled' => true,
            ))
            ->add('activationToken', TextType::class, array(
                'label' => 'Token',
                'disabled' => true,
            ))
            ->add('role', ChoiceType::class, array(
                'label' => 'Uprawnienia',
                'choices'  => array(
                    'Supervisior' => 'supervisior',
                    'Client' => 'client',
                ),
            ))
            ->add('cinema', EntityType::class, array(
                'label' => 'Kino',
                'class' => 'AppBundle:Cinema',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => ''
            ))
            ->add('city', EntityType::class, array(
                'label' => 'Miasto',
                'class' => 'AppBundle:City',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => ''
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Aktualizuj',
                'attr' => array('class' => 'btn btn-success pull-right')
            ));
    }
}