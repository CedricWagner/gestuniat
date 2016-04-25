<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SectionFullType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('nom',TextType::class,array('label' => 'Nom'))
            ->add('subventions',TextType::class,array('label' => 'Subventions'))
            ->add('destDernierListing',TextType::class,array('label' => 'Destinataire du dernier listing'))
            ->add('dateDernierListing',DateType::class,array('label' => 'Date du dernier listing','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('numBulletin',null,array('label' => 'Bulletin municipal'))
            ->add('dateRemiseBulletin',DateType::class,array('label' => 'Date de remise du bulletin','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('infosComp',TextareaType::class,array('label' => 'Informations complémentaires'))
            ->add('dateDerniereAG',DateType::class,array('label' => 'Date de la dernière AG','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('isActive',CheckboxType::class,array('label' => 'Est active'))
            ->add('save',SubmitType::class,array('label' => 'Enregistrer'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Section'
        ));
    }
}