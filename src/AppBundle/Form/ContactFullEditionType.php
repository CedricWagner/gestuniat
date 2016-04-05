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
            ->add('prenom',TextType::class,array('label' => 'Prénom'))
            ->add('mail',TextType::class,array('label' => 'Email'))
            ->add('telFixe',TextType::class,array('label' => 'Tél. fixe'))
            ->add('telPort',TextType::class,array('label' => 'Tél. portable'))
            ->add('adresse',TextType::class,array('label' => 'Adresse'))
            ->add('adresseComp',TextareaType::class,array('label' => 'Complément d\'adresse'))
            ->add('cp',TextType::class,array('label' => 'Code postal'))
            ->add('commune',TextType::class,array('label' => 'Commune'))
            ->add('bp',TextType::class,array('label' => 'Boite postale'))
            ->add('pays',TextType::class,array('label' => 'Pays'))
            ->add('dateNaissance',BirthdayType::class,array('label' => 'Date de naissance'))
            ->add('lieuNaissance',TextType::class,array('label' => 'Lieu de naissance'))
            ->add('dateDeces',DateType::class,array('label' => 'Date de décès','years'=>range(1950,$currentYear->format('Y'))))
            ->add('mentionDeces',TextareaType::class,array('label' => 'Mention décès'))
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