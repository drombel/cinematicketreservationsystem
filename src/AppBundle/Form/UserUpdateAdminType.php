<?php
/**
 * Created by PhpStorm.
 * User: Julek
 * Date: 2018-12-02
 * Time: 17:55
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

class UserUpdateAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Imię',
                'attr' => array(
                    'class' => 'form-control',
                    'autofocus' => true,
                    'pattern' => '[a-żA-Ż]{3,}',
                    'required' => true,
                    'title' => 'Minimum 3 znaki'),
            ))
            ->add('surname', TextType::class, array(
                'label' => 'Nazwisko',
                'attr' => array(
                    'class' => 'form-control',
                    'pattern' => '[a-żA-Ż]{3,}',
                    'required' => true,
                    'title' => 'Minimum 3 znaki'),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail',
                'attr' => array(
                    'class' => 'form-control',
                    'pattern' => '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'),
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