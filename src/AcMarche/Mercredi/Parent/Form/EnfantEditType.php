<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sexes = EnfanceData::getSexes();
        $anneesScolaires = ScolaireService::getAnneesScolaires();

        $builder
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
                'numero_national',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'ecole',
                EntityType::class,
                array(
                    'class' => Ecole::class,
                    'required' => true,
                    'placeholder' => 'Choisissez son école',
                )
            )
            ->add(
                'annee_scolaire',
                ChoiceType::class,
                array(
                    'choices' => $anneesScolaires,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
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
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 4),
                )
            )
            ->add(
                'accompagnateurs',
                CollectionType::class,
                array(
                    'entry_type' => TextType::class,
                    'entry_options' => array(),
                    'prototype' => true,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                )
            )
        /*    ->add(
                'image',
                FileType::class,
                array(
                    'label' => "Photo de l'enfant",
                    'required' => false,
                )
            )*/
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Enfant::class,
            )
        );
    }
}
