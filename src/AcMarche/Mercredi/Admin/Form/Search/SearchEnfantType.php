<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEnfantType extends AbstractType
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;

    public function __construct(EcoleRepository $ecoleRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $scolaires = ScolaireService::getAnneesScolaires();
        $ecoles = $this->ecoleRepository->getForSearch();

        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => false,
                    'attr' => array('placeholder' => 'Nom'),
                )
            )
            ->add(
                'ecole',
                ChoiceType::class,
                array(
                    'required' => false,
                    'placeholder' => 'Choisissez son école',
                    'choices' => $ecoles,
                    'attr' => array(),
                )
            )
            ->add(
                'annee_scolaire',
                ChoiceType::class,
                array(
                    'choices' => $scolaires,
                    'required' => false,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                    'attr' => array(),
                )
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Rechercher',
                )
            )
            ->add(
                'raz',
                SubmitType::class,
                array(
                    'label' => 'raz',
                    'attr' => array(
                        'class' => 'btn-sm btn-success',
                        'title' => 'Search raz',
                    ),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array()
        );
    }
}
