<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RemiseTimbreType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('isAnnuel',ChoiceType::class,array(
                'label' => 'Periodicité',
                'choices' => array(' Annuel' => true, ' Semestriel' => false),
                'choices_as_values' => true,
                'expanded' => true,
                'multiple' => false,
                ))
            ->add('annee',null,array('label' => 'Année'))
            ->add('dateRemise',DateType::class,array('label' => 'Date remise','years'=>range($currentYear->format('Y'),2005),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('nbEmis',null,array('label' => 'Timbres émis'))
            ->add('nbRemis',null,array('label' => 'Timbres retournés'))
            ->add('nbPayes',null,array('label' => 'Timbres payés'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\RemiseTimbre'
        ));
    }
}