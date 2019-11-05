<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\DataTransformer\TuteurToNumberTransformer;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{
    private $paiement;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['em'];
        $this->paiement = $options['paiement'];

        $transformer_tuteur = new TuteurToNumberTransformer($entityManager);
        $enfants = [];
        $ordres = EnfanceData::getOrdres();

        $types_paiement = EnfanceData::getTypePaiement();
        $modes_paiement = EnfanceData::getModePaiement();

        if ($this->paiement->getTuteur()) {
            $tuteur_id = $this->paiement->getTuteur()->getId();
            $enfants = $entityManager->getRepository(Tuteur::class)->getEnfants($tuteur_id);
        }

        $builder
            ->add(
                'montant',
                NumberType::class,
                [
                    'required' => true,
                    'help' => 'Uniquement les chiffres',
                    'label' => 'Montant',
                ]
            )
            ->add(
                'date_paiement',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => ['class' => 'datepicker', 'placeholder' => '00-00-000'],
                ]
            )
            ->add(
                'enfant',
                EntityType::class,
                [
                    'class' => Enfant::class,
                    'choices' => $enfants,
                    'label' => 'Pour quel enfant',
                    'attr' => [],
                ]
            )
            ->add(
                'type_paiement',
                ChoiceType::class,
                [
                    'required' => false,
                    'label' => 'Type de paiement',
                    'choices' => $types_paiement,
                    'attr' => [],
                ]
            )
            ->add(
                'mode_paiement',
                ChoiceType::class,
                [
                    'required' => false,
                    'label' => 'Mode de paiement',
                    'choices' => $modes_paiement,
                    'attr' => [],
                ]
            )
            ->add(
                'ordre',
                ChoiceType::class,
                [
                    'choices' => $ordres,
                    'placeholder' => 'Aucun ordre',
                    'required' => false,
                    'attr' => [],
                ]
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [],
                ]
            )
            ->add(
                'cloture',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Clôturé',
                    'help' => 'Cochez si le paiement contient le maximum de présences',
                ]
            )
            ->add(
                $builder->create('tuteur', HiddenType::class)
                    ->addModelTransformer($transformer_tuteur)
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Paiement::class,
                'paiement' => null,
            ]
        );

        $resolver->setRequired(
            [
                'em',
            ]
        );

        $resolver->setAllowedTypes('em', ObjectManager::class);
    }
}
