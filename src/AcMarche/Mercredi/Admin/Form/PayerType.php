<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Plaine\Form\Type\EnfantSelectorType;
use AcMarche\Mercredi\Plaine\Form\Type\TuteurSelectorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayerType extends AbstractType
{
    private $presences;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->presences = $options['presences'];

        $builder->add('tuteur', TuteurSelectorType::class);
        $builder->add('enfant', EnfantSelectorType::class);

        $builder
            ->add('presences', EntityType::class, [
                'class' => Presence::class,
                'choices' => $this->presences,
                'choice_label' => 'avecPrix',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Choisissez une ou plusieurs dates',
                'attr' => ['style' => 'height:150px;'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
            'paiement' => null,
            'presences' => [],
        ]);
    }
}
