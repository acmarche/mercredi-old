<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'from',
                TextType::class,
                [
                    'label' => 'De',
                    'attr' => ['readonly' => true],
                ]
            )
            ->add(
                'sujet',
                TextType::class,
                array(
                    "required" => true,
                )
            )
            ->add(
                'texte',
                TextareaType::class,
                array(
                    "required" => true,
                    "attr" => array('rows' => 10, 'cols' => 50),
                )
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Pièce jointe',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Message::class,
            )
        );

    }


}
