<?php

namespace AcMarche\Mercredi\Security\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('plainPassword');
    }

    public function getParent()
    {
        return UtilisateurType::class;
    }
}
