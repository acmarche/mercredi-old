<?php

namespace AcMarche\Mercredi\Plaine\Form\Type;

use AcMarche\Mercredi\Plaine\Form\DataTransformer\PlaineToNumberTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineSelectorType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $om;

    public function __construct(EntityManagerInterface $om)
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
        $resolver->setDefaults([
            'invalid_message' => 'The selected plaine does not exist',
        ]);
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
