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

class ContratAgrrType extends AbstractType
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
                    'Ayant droit' => 'AD',
                    )
                ))
            ->add('nomPrenomAD',TextType::class,array('label' => 'Nom Prénom (si ayant droit)'))
            ->add('etat',ChoiceType::class,array(
                'label' => 'Etat','choices'=> array(
                    'En attente' => '0',
                    'Actif' => '1',
                    'Résilié' => '2',
                    )
                ))
            ->add('option',TextType::class,array('label' => 'Option'))
            ->add('optionPrec',TextType::class,array('label' => 'Option précédente'))
            ->add('comGarantie',TextType::class,array('label' => 'Com. garanties'))
            ->add('regimeAffiliation',EntityType::class,array('label' => 'Régime d\'affiliation','class'=>'AppBundle:RegimeAffiliation','placeholder'=>'Aucun','choice_label'=>'label'))
            ->add('dateEffet',DateType::class,array('label' => 'Date d\'effet','years'=>range($currentYear->format('Y'),1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dateModif',DateType::class,array('label' => 'Date de modification','years'=>range($currentYear->format('Y'),1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dateEffetModif',DateType::class,array('label' => 'Date effet de la modification','years'=>range($currentYear->format('Y'),1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dateRes',DateType::class,array('label' => 'Date de résiliation','years'=>range($currentYear->format('Y'),1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dateEffetRes',DateType::class,array('label' => 'Date effet res.','years'=>range($currentYear->format('Y'),1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dateConfirmAGGR',DateType::class,array('label' => 'Date confirmation AGRR.','years'=>range($currentYear->format('Y'),1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('isAutreMutu',CheckboxType::class,array('label' => 'Autre mutuelle'))
            ->add('commentaire',TextareaType::class,array('label' => 'Commentaire'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ContratPrevoyance'
        ));
    }
}