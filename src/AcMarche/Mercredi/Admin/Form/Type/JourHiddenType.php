<?php

namespace AcMarche\Mercredi\Admin\Form\Type;

use AcMarche\Mercredi\Admin\Form\DataTransformer\JourToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class JourHiddenType extends AbstractType
{
    /**
     * @var JourToNumberTransformer
     */
    protected $transformer;

    public function __construct(JourToNumberTransformer $jourToNumberTransformer)
    {
        $this->transformer = $jourToNumberTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
