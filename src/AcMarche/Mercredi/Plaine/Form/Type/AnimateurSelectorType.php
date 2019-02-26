<?php

namespace AcMarche\Mercredi\Plaine\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use AcMarche\Mercredi\Admin\Form\DataTransformer\AnimateurToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimateurSelectorType extends AbstractType
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
        $transformer = new AnimateurToNumberTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected animateur does not exist',
        ));
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
