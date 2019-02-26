<?php

namespace AcMarche\Mercredi\Admin\Form\Quick;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantQuickType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'birthday',
                DateType::class,
                array(
                    'label' => "Né le",
                    'widget' => 'text',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'birthday-text'),
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
                    'required' => true,
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
                'data_class' => Enfant::class,
            )
        );
    }
}
