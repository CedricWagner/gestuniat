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

class ContratObsequeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('numContrat',TextType::class,array('label' => 'Numéro de contrat'))
            ->add('cible',ChoiceType::class,array(
                'label' => 'Cible','choices'=> array(
                    'Membre principal' => 'CONTACT',
                    'Membre conjoint' => 'CONJOINT',
                    )
                ))
            ->add('option',TextType::class,array('label' => 'Option'))
            ->add('dateEffet',DateType::class,array('label' => 'Date d\'effet','years'=>range($currentYear->format('Y')+20,1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dateVersement',DateType::class,array('label' => 'Date de versement','years'=>range($currentYear->format('Y')+20,1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('isResilie',CheckboxType::class,array('label' => 'Contrat résilié'))
            ->add('dateRes',DateType::class,array('label' => 'Date de résiliation','years'=>range($currentYear->format('Y')+20,1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('commentaire',TextareaType::class,array('label' => 'Commentaire'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ContratPrevObs'
        ));
    }
}