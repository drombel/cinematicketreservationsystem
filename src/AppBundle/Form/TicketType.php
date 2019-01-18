<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('cinemaHallHasMovieId', EntityType::class, [
                'class' => 'AppBundle:Cinema_hall_has_Movie',

            ])
            ->add('seatId')
            ->add('date',DateType::Class, array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')+5),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail',
                'attr' => array(
                    'class' => 'form-control',
                    'pattern' => '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'),
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
