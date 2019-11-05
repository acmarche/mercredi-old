<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use AcMarche\Mercredi\Security\Repository\GroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUtilisateurType extends AbstractType
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groupes = $this->groupRepository->getForSearch();

        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            )
            ->add(
                'groupe',
                ChoiceType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez un groupe',
                    'choices' => $groupes,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Rechercher',
                ]
            )
            ->add(
                'raz',
                SubmitType::class,
                [
                    'label' => 'raz',
                    'attr' => [
                        'class' => 'btn-sm btn-success',
                        'title' => 'Search raz',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            []
        );
    }
}
