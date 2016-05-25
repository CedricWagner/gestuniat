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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CotisationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('dateCreation',DateType::class,array('label' => 'Date de demande','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('isSemestriel',CheckboxType::class,array('label' => 'Semestriel'))
            ->add('montant',TextType::class,array('label' => 'Montant'))
            ->add('datePaiement',DateType::class,array('label' => 'Date de paiement','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('moyenPaiement',EntityType::class,array('label' => 'Moyen de paiement','class'=>'AppBundle:MoyenPaiement','choice_label'=>'label'))
            
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cotisation'
        ));
    }
}