<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoordonneesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'adresse',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'code_postal',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'localite',
                TextType::class,
                array(
                    'required' => true,
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
                    'label' => 'Téléphone du bureau',
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
                    'label' => 'Email',
                )
            )
            ->add(
                'telephone_conjoint',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Téléphone',
                )
            )
            ->add(
                'telephone_bureau_conjoint',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Téléphone du bureau',
                )
            )
            ->add(
                'gsm_conjoint',
                TextType::class,
                array(
                    'label' => 'Gsm',
                    'required' => false,
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
