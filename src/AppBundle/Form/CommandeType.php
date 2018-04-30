<?php

namespace AppBundle\Form;

use AppBundle\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateBillet', DateType::class,
                array(
                    'widget'    => 'single_text',
                    'attr'      => ['class' => 'datepicker2'],
                    'html5'     => false,
                    'format'    => 'dd-MM-yyyy',
                )
            )
            ->add('demiJournee', ChoiceType::class,
                array(
                    'attr'      => ['class' => 'typeBilletJour'],
                    'choices'   => array(
                        'Journée'       => false,
                        'Demi-journée'  => true
                    )
                )
            )
            ->add('billets', CollectionType::class,
                array(
                    'entry_type'    => BilletType::class,
                    'allow_add'     => true,
                    'allow_delete'  => true
                )
            )
            ->add('Valider', SubmitType::class, array(
                'attr' => array('class' => 'btn-success btn-lg float-right')
            ))
        ;

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Commande::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_commandes';
    }


}
