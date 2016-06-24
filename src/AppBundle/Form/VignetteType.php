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
use AppBundle\Repository\DossierRepository;

class VignetteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $entity = $options['data'];

        $idContact = $entity->getContact()->getId();

        $builder
            ->add('dateDemande',DateType::class,array('label' => 'Date de la demande','years'=>range($currentYear->format('Y')+20,1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('dossier',EntityType::class,array(
                'label' => 'Dossier',
                'class'=>'AppBundle:Dossier',
                'placeholder'=>'Aucun',
                'choice_label'=>'nom',
                'query_builder' => function (DossierRepository $er) use ($idContact){
                    return $er->getByContactQuery($idContact);
                },
                ))
            ->add('montant',NumberType::class,array('label' => 'Montant demandé'))
            ->add('moyenPaiement',EntityType::class,array('label' => 'Moyen de paiement','class'=>'AppBundle:MoyenPaiement','placeholder'=>'Indéfini','choice_label'=>'label'))
            ->add('datePaiement',DateType::class,array('label' => 'Date du paiement','years'=>range($currentYear->format('Y')+20,1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Vignette'
        ));
    }
}