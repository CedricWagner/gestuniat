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

class ProcurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('date',DateType::class,array('label' => 'Date d\'obtention','years'=>range($currentYear->format('Y')+20,1950),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('lieu',TextType::class,array('label' => 'Lieu'))
            ->add('statut',TextType::class,array('label' => 'Statut'))
            ->add('isRecevoirCorres',CheckboxType::class,array('label' => 'Recevoir à ma place toute correspondance et me transmettre tout document nécessitant ma décision et/ou ma signature'))
            ->add('isCourrierRep',CheckboxType::class,array('label' => 'vous faire parvenir tous les courriers de réponse'))
            ->add('isCompletQues',CheckboxType::class,array('label' => 'compléter tout questionnaire ou apporter toute information nécessaire à l\'étude de mon dossier'))
            ->add('isDemandeRens',CheckboxType::class,array('label' => 'vous adresser une demande de renseignement ou une information'))
            ->add('isRelContest',CheckboxType::class,array('label' => 'Relatif à ma contestation'))
            ->add('isRelPaiement',CheckboxType::class,array('label' => 'Relatif à toute information concernant mes paiements'))
            ->add('isRelChangement',CheckboxType::class,array('label' => 'Relatif à mon changement d\'adresse ou de mode de paiement'))
            ->add('mention',TextareaType::class,array('label' => 'Mention'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Procuration'
        ));
    }
}