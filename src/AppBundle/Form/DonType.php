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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('date',DateType::class,array('label' => 'Date','years'=>range(1950,$currentYear->format('Y')+20),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('montant',NumberType::class,array('label' => 'Montant'))
            ->add('moyenPaiement',EntityType::class,array('label' => 'Moyen de paiement','class'=>'AppBundle:MoyenPaiement','placeholder'=>'Indéfini','choice_label'=>'label'))
            ->add('isAnonyme',CheckboxType::class,array('label' => 'Don anonyme'))
            ->add('intermediaire',TextType::class,array('label' => 'Intermédiaire'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Don'
        ));
    }
}