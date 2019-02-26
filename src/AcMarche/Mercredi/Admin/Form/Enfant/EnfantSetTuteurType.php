<?php

namespace AcMarche\Mercredi\Admin\Form\Enfant;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Form\Type\TuteurHiddenType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantSetTuteurType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * JustAFormType constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tuteur', TuteurHiddenType::class)
            ->add(
                'autocompletion',
                TextType::class,
                array(
                    'mapped' => false,
                    'label' => ' ',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Nom',
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
            array(
                'data_class' => EnfantTuteur::class,
            )
        );
    }
}
