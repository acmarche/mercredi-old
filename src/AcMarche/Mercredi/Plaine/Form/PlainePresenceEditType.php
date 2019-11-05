<?php

/**
 * Pour ajouter une date a un enfant a une plaine.
 */

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresenceEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enfant = $options['enfant'];
        $ordres = EnfanceData::getOrdres();
        $absences = EnfanceData::getAbsenceTxt();

        $builder
            ->add('tuteur', EntityType::class, [
                'class' => Tuteur::class,
                'query_builder' => function (TuteurRepository $repo) use ($enfant) {
                    return $repo->getTuteursForList($enfant);
                },
            ])
            ->add('ordre', ChoiceType::class, [
                'choices' => $ordres,
                'required' => false,
                'placeholder' => 'Choisir un ordre',
                'attr' => [],
            ])
            ->add('absent', ChoiceType::class, [
                'required' => true,
                'label' => 'Absent ?',
                'choices' => $absences,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlainePresence::class,
            'enfant' => null,
        ]);
    }
}
