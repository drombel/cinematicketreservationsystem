<?php
/**
 * Created by PhpStorm.
 * User: Julek
 * Date: 2019-01-04
 * Time: 20:51
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class RemindPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, array(
            'label' => 'Podaj email, na który zostanie wysłane nowe hasło',
            'required' => true,
            'attr' => array(
                'class' => 'form-control',
                'pattern' => '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'),
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'Wyślij',
            'attr' => array('class' => 'btn btn-success pull-right')
        ));
    }
}