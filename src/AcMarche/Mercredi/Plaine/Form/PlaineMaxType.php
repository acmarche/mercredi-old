<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlaineMax;
use AcMarche\Mercredi\Plaine\Form\Type\PlaineSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineMaxType extends AbstractType
{
    protected $label = false;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupe', TextType::class,[
                'attr'=>['readonly'=>true]
            ])
            ->add('maximum', IntegerType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => PlaineMax::class,
            )
        );
    }
}
