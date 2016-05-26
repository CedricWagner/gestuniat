<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ContactFullEditionType extends AbstractType
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
            ->add('nomJeuneFille',TextType::class,array('label' => 'Nom de jeune fille'))
            ->add('prenom',TextType::class,array('label' => 'Prénom'))
            ->add('mail',TextType::class,array('label' => 'Email'))
            ->add('telFixe',TextType::class,array('label' => 'Tél. fixe'))
            ->add('telPort',TextType::class,array('label' => 'Tél. portable'))
            ->add('adresse',TextType::class,array('label' => 'Adresse'))
            ->add('adresseComp',TextType::class,array('label' => 'Complément d\'adresse'))
            ->add('cp',TextType::class,array('label' => 'Code postal'))
            ->add('commune',TextType::class,array('label' => 'Commune'))
            ->add('bp',TextType::class,array('label' => 'Boite postale'))
            ->add('pays',TextType::class,array('label' => 'Pays'))
            ->add('dateNaissance',BirthdayType::class,array('label' => 'Date de naissance','placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('lieuNaissance',TextType::class,array('label' => 'Lieu de naissance'))
            ->add('dateDeces',DateType::class,array('label' => 'Date de décès','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('mentionDeces',TextType::class,array('label' => 'Mention décès'))
            ->add('dateAdhesion',DateType::class,array('label' => 'Date d\'adhésion','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('typeAdhesion',ChoiceType::class,array('label'=>'Type d\'adhésion','choices'=>array(
                    'Annuel'=>'ANNUEL',
                    'Semestriel'=>'SEMESTRIEL',
                )))
            ->add('statutJuridique',EntityType::class,array('label' => 'Type de profil','class'=>'AppBundle:StatutJuridique','choice_label'=>'label'))
            ->add('statutMatrimonial',EntityType::class,array('label' => 'Statut matrimonial','class'=>'AppBundle:StatutMatrimonial','choice_label'=>'label','placeholder' => 'Inconnu'))
            ->add('civilite',EntityType::class,array('label' => 'Civilité','class'=>'AppBundle:Civilite','choice_label'=>'label','placeholder' => 'Inconnu'))
            ->add('fonctionSection',EntityType::class,array('label' => 'Fonction de section','class'=>'AppBundle:FonctionSection','choice_label'=>'label','placeholder' => 'Aucune'))
            ->add('dateDelivranceFonc',DateType::class,array('label' => 'Date de délivrance de la fonction','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('fonctionGroupement',EntityType::class,array('label' => 'Fonction de groupement','class'=>'AppBundle:FonctionGroupement','choice_label'=>'label','placeholder' => 'Aucune'))
            ->add('section',EntityType::class,array('label' => 'Section','class'=>'AppBundle:Section','choice_label'=>'nom','placeholder' => 'Aucune'))
            ->add('encaisseur',TextType::class,array('label' => 'Nom encaisseur'))
            ->add('isDossierPaye',CheckboxType::class,array('label' => 'Frais de dossier payé'))
            ->add('isCA',CheckboxType::class,array('label' => 'Est membre du CA'))
            ->add('fonctionRepresentation',TextType::class,array('label' => 'Fonction représentation'))
            ->add('numSecu',TextType::class,array('label' => 'Numéro de sécu'))
            ->add('dateCIF',DateType::class,array('label' => 'Date CIF','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('isRentier',CheckboxType::class,array('label' => 'Est dépositaire rentier'))
            ->add('nbRentiers',null,array('label' => 'Nombre de rentiers'))
            ->add('isBI',CheckboxType::class,array('label' => 'Reçoit le bulletin d\'information'))
            ->add('isCourrier',CheckboxType::class,array('label' => 'Courrier'))
            ->add('isEnvoiIndiv',CheckboxType::class,array('label' => 'Envoi individuel'))
            ->add('isOffreDecouverte',CheckboxType::class,array('label' => 'Profite de l\'offre découverte'))
            ->add('dateOffreDecouverte',DateType::class,array('label' => 'Début de l\'offre découverte','years'=>range(1950,$currentYear->format('Y')),'placeholder' => array(
                    'year' => 'année', 'month' => 'mois', 'day' => 'jour'
                )))
            ->add('save',SubmitType::class,array('label' => 'Enregistrer'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contact'
        ));
    }
}