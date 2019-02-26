<?php

namespace AcMarche\Mercredi\Admin\Form\Tuteur;

use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sexes = EnfanceData::getSexes();
        $civilites = EnfanceData::getCivilites();

        $builder
            ->add(
                'civilite',
                ChoiceType::class,
                array(
                    'required' => false,
                    'choices' => $civilites,
                    'placeholder' => 'Choisissez une civilité',
                )
            )
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'adresse',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'code_postal',
                IntegerType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'localite',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'telephone',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'telephone_bureau',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'gsm',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'sexe',
                ChoiceType::class,
                array(
                    'required' => false,
                    'choices' => $sexes,
                    'placeholder' => 'Choisissez son sexe',
                )
            )
            ->add(
                'conjoint',
                TextType::class,
                array(
                    'label' => 'Relation entre les conjoints',
                    'required' => false,
                    'help' => 'Belle-mère, Maman...',
                )
            )
            ->add(
                'nom_conjoint',
                TextType::class,
                array(
                    'label' => 'Nom du conjoint',
                    'required' => false,
                )
            )
            ->add(
                'prenom_conjoint',
                TextType::class,
                array(
                    'label' => 'Prénom du conjoint',
                    'required' => false,
                )
            )
            ->add(
                'email_conjoint',
                EmailType::class,
                array(
                    'required' => false,
                    'label' => 'Email du conjoint',
                )
            )
            ->add(
                'telephone_conjoint',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Téléphone du conjoint',
                )
            )
            ->add(
                'telephone_bureau_conjoint',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Téléphone du bureau du conjoint',
                )
            )
            ->add(
                'gsm_conjoint',
                TextType::class,
                array(
                    'label' => 'Gsm du conjoint',
                    'required' => false,
                )
            )
            ->add(
                'composition_menage',
                CheckboxType::class,
                array(
                    'label' => 'Composition de ménage',
                    'required' => false,
                )
            )
            ->add(
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 8),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Tuteur::class,
            )
        );
    }
}
