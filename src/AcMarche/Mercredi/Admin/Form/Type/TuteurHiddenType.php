<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 28/08/18
 * Time: 10:09.
 */

namespace AcMarche\Mercredi\Admin\Form\Type;

use AcMarche\Mercredi\Admin\Form\DataTransformer\TuteurToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Entity hidden custom type class definition.
 */
class TuteurHiddenType extends AbstractType
{
    /**
     * @var DataTransformerInterface
     */
    private $transformer;

    /**
     * Constructor.
     *
     * @param DataTransformerInterface $transformer
     */
    public function __construct(TuteurToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // attach the specified model transformer for this entity list field
        // this will convert data between object and string formats
        $builder->addModelTransformer($this->transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
