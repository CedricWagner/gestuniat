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

class SuiviDefaultType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('texte',TextareaType::class,array('label' => 'Description'))
            ->add('dateEcheance',DateType::class,array('label' => 'Échéance','years'=>range($currentYear->format('Y'),$currentYear->format('Y')+20),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('isOk',CheckboxType::class,array('label' => 'Terminé'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Suivi'
        ));
    }
}