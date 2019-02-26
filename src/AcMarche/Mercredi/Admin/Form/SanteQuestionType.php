<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SanteQuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'intitule',
            TextType::class
        )
            ->add(
                'complement',
                CheckboxType::class,
                [
                    'label' => 'Un complément d\'information est-il nécessaire ?',
                    'help' => 'Si oui cochez la case',
                    'required' => false,
                ]
            )
            ->add(
                'complementLabel',
                TextType::class,
                [
                    'label' => 'Texte d\'aide pour le complément',
                    'help' => 'Par ex: Date de vaccin, quel type de médicaments...',
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => SanteQuestion::class,
            )
        );
    }
}
