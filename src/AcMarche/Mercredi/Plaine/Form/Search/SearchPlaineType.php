<?php

namespace AcMarche\Mercredi\Plaine\Form\Search;

use AcMarche\Mercredi\Admin\Entity\EnfanceData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPlaineType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('intitule', TextType::class, array(
                    'required' => false,
                    'attr' => array('placeholder' => 'Nom')
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'Rechercher',
                ))
                ->add('raz', SubmitType::class, array(
                    'label' => 'raz',
                    'attr' => array('class' => 'btn-sm btn-success',
                        'title' => 'Search raz')
                ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }
}