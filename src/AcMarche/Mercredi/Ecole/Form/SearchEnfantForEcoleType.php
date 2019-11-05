<?php

namespace AcMarche\Mercredi\Ecole\Form;

use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchEnfantForEcoleType extends AbstractType
{
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var User|string
     */
    private $user;

    public function __construct(
        JourRepository $jourRepository,
        EcoleRepository $ecoleRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->jourRepository = $jourRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->tokenStorage = $tokenStorage;

        $this->user = $this->tokenStorage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ecoles = $this->ecoleRepository->getForSearchByUser($this->user);
        $jours = $this->jourRepository->getForEcoleToSearch();

        $builder
            ->add(
                'jour',
                ChoiceType::class,
                [
                    'choices' => $jours,
                    'placeholder' => 'Choisissez une date',
                    'required' => false,
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            )
            ->add(
                'ecole',
                ChoiceType::class,
                [
                    'required' => true,
                    'choices' => $ecoles,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Rechercher',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
