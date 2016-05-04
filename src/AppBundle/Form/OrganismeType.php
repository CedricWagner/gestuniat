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

class OrganismeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('typeOrganisme',EntityType::class,array('label' => 'Type','class'=>'AppBundle:TypeOrganisme','placeholder'=>'Indéfini','choice_label'=>'label'))
            ->add('nom',TextType::class,array('label' => 'Nom'))
            ->add('civilite',EntityType::class,array('label' => 'Civilité','class'=>'AppBundle:Civilite','placeholder'=>'Indéfini','choice_label'=>'label'))
            ->add('nomTitulaire',TextType::class,array('label' => 'Nom du titulaire'))
            ->add('fonctionTitulaire',TextType::class,array('label' => 'Fonction du titulaire'))
            ->add('adresse',TextType::class,array('label' => 'Adresse'))
            ->add('adresseComp',TextareaType::class,array('label' => 'Complément d\'adresse'))
            ->add('cp',TextType::class,array('label' => 'Code postal'))
            ->add('ville',TextType::class,array('label' => 'Ville'))
            ->add('pays',TextType::class,array('label' => 'Pays'))
            ->add('bp',TextType::class,array('label' => 'Boîte postale'))
            ->add('telephone',TextType::class,array('label' => 'Téléphone'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Organisme'
        ));
    }
}