<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class SetSeatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rows', IntegerType::class, array(
                'label' => 'Ilość rzędów',
                'attr' => array('class' => 'form-control', 'autofocus' => true),
                'required' => true,
                'attr' => array('min' => 1, 'max' => 50)
            ))
            ->add('cols', IntegerType::class, array(
                'label' => 'Ilość kolumn',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'attr' => array('min' => 1, 'max' => 50)
            ));
    }
}