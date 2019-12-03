<?php

namespace AcMarche\Mercredi\Admin\Form\Enfant;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Form\Type\TuteurHiddenType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantSetTuteurType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $objectManager;

    /**
     * JustAFormType constructor.
     */
    public function __construct(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tuteur', TuteurHiddenType::class)
            ->add(
                'autocompletion',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => ' ',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Nom',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => EnfantTuteur::class,
            ]
        );
    }
}
