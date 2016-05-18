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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class OperateurType extends AbstractType
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
            ->add('role',ChoiceType::class,array('label' => 'Rôle','choices'=>array(
                    'Spectateur'=>'SPECTATOR',
                    'Utilisateur'=>'USER',
                    'Juriste'=>'JURIST',
                    'Administrateur'=>'ADMIN',
                    'Supprimé'=>'DELETED',
                )))
            ->add('login',TextType::class,array('label' => 'Login'))
            ->add('mdp',PasswordType::class,array('label' => 'Mot de passe','always_empty'=>true))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Operateur'
        ));
    }
}