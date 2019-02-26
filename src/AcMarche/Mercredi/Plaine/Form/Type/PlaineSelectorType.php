<?php

namespace AcMarche\Mercredi\Plaine\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use AcMarche\Mercredi\Plaine\Form\DataTransformer\PlaineToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineSelectorType extends AbstractType
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PlaineToNumberTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected plaine does not exist',
        ));
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
