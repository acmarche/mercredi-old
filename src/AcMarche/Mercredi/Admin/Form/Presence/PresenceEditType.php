<?php

namespace AcMarche\Mercredi\Admin\Form\Presence;

use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Events\PresenceFieldSubscriber;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PaiementRepository;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceEditType extends AbstractType
{
    /**
     * j'ajoute l'entity a mon form
     * pour eviter de proposer des dates pour
     * lesquels il est dja inscrit.
     *
     * @var Presence
     */
    private $presence;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;

    public function __construct(EnfantTuteurRepository $enfantTuteurRepository)
    {
        $this->enfantTuteurRepository = $enfantTuteurRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->presence = $options['presence'];
        $this->presence = $builder->getForm()->getData();

        $ordres = EnfanceData::getOrdres();
        $absences = EnfanceData::getAbsenceTxt();

        $builder
            ->add(
                'reduction',
                null,
                [
                    'label' => 'Réduction',
                ]
            )
            ->add(
                'absent',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'Absent ?',
                    'choices' => $absences,
                ]
            )
            ->add(
                'paiement',
                EntityType::class,
                [
                    'placeholder' => 'Payer avec...',
                    'required' => false,
                    'class' => Paiement::class,
                    'query_builder' => function (PaiementRepository $cr) {
                        return $cr->getForList($this->presence->getTuteur());
                    },
                ]
            )
            ->add(
                'ordre',
                ChoiceType::class,
                [
                    'choices' => $ordres,
                    'required' => false,
                ]
            )
            ->addEventSubscriber(new PresenceFieldSubscriber($this->enfantTuteurRepository));

        /*  $tuteurs = array();
          $enfant = $this->presence->getEnfant();

          $enfant_tuteurs = $enfant->getTuteurs();
          foreach ($enfant_tuteurs as $enfant_tuteur) {
              $tuteurt = $enfant_tuteur->getTuteur();
              $tuteurs[] = $tuteurt;
          }

          if (count($tuteurs) > 1) {
              $builder->add(
                  'tuteur',
                  EntityType::class,
                  array(
                      'choices' => $tuteurs,
                      'class' => Tuteur::class,
                      'attr' => array(),
                  )
              );
          }*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Presence::class,
                'presence' => null,
            ]
        );
    }
}
