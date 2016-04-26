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

class PermanenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currentYear = new \DateTime(date('Y'));

        $builder
            ->add('typeTournee',EntityType::class,array('label' => 'Type de tournée','class'=>'AppBundle:TypeTournee','choice_label'=>'label'))
            ->add('label',TextType::class,array('label' => 'Description'))
            ->add('lieu',TextType::class,array('label' => 'Lieu'))
            ->add('horaire',TextType::class,array('label' => 'Horaire'))
            ->add('periodicite',EntityType::class,array('label' => 'Periodicité','class'=>'AppBundle:Periodicite','choice_label'=>'label'))
            ->add('militants',TextareaType::class,array('label' => 'Militants'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Permanence'
        ));
    }
}