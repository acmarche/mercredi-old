<?php

namespace AcMarche\Mercredi\Admin\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use AcMarche\Mercredi\Admin\Form\DataTransformer\JourToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourHiddenType extends AbstractType
{
    /**
     * @var JourToNumberTransformer $transformer
     */
    protected $transformer;

    /**
     * @param JourToNumberTransformer $jourToNumberTransformer
     */
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
