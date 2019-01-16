<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cinemaHallHasMovieId')
            ->add('seatId')
            ->add('email', EmailType::class, array(
                'label' => 'E-mail',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('status', ChoiceType::class, array(
            'choices'  => array(
                'Zatwierdzono' => 'Ok',
                'W toku' => 'Pending',
                'Anulowano' => 'Canceled',
            )))
            ->add('userId');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ticket'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ticket';
    }


}
