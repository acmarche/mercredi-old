<?php

namespace AcMarche\Mercredi\Admin\Form;

use Doctrine\Common\Persistence\ObjectManager;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\DataTransformer\TuteurToNumberTransformer;
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['em'];
        $this->paiement = $options['paiement'];

        $transformer_tuteur = new TuteurToNumberTransformer($entityManager);
        $enfants = array();
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
                array(
                    'required' => true,
                    'help'=>'Uniquement les chiffres',
                    'label' => 'Montant',
                )
            )
            ->add(
                'date_paiement',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'datepicker', 'placeholder' => '00-00-000'),
                )
            )
            ->add(
                'enfant',
                EntityType::class,
                array(
                    'class' => Enfant::class,
                    'choices' => $enfants,
                    'label' => 'Pour quel enfant',
                    'attr' => array(),
                )
            )
            ->add(
                'type_paiement',
                ChoiceType::class,
                array(
                    'required' => false,
                    'label' => 'Type de paiement',
                    'choices' => $types_paiement,
                    'attr' => array(),
                )
            )
            ->add(
                'mode_paiement',
                ChoiceType::class,
                array(
                    'required' => false,
                    'label' => 'Mode de paiement',
                    'choices' => $modes_paiement,
                    'attr' => array(),
                )
            )
            ->add(
                'ordre',
                ChoiceType::class,
                array(
                    'choices' => $ordres,
                    'placeholder' => 'Aucun ordre',
                    'required' => false,
                    'attr' => array(),
                )
            )
            ->add(
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array(),
                )
            )
            ->add(
                'cloture',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' => 'Clôturé',
                    'help' => 'Cochez si le paiement contient le maximum de présences',
                )
            )
            ->add(
                $builder->create('tuteur', HiddenType::class)
                    ->addModelTransformer($transformer_tuteur)
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Paiement::class,
                'paiement' => null,
            )
        );

        $resolver->setRequired(
            array(
                'em',
            )
        );

        $resolver->setAllowedTypes('em', ObjectManager::class);
    }
}
