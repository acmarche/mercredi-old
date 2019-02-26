<?php

namespace AcMarche\Mercredi\Plaine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlaineEditType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->remove('jours')
        ;
    }
    
    public function getParent()
    {
        return PlaineType::class;
    }
}
