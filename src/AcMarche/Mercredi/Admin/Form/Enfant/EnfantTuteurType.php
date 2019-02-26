<?php

namespace AcMarche\Mercredi\Admin\Form\Enfant;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantTuteurType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'relation',
            null,
            array(
                'label' => 'Relation',
                'help' => '(Père, Maman, Belle-maman...)',
            )
        );
        $builder->add('ordre', null);
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
