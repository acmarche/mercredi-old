<?php

namespace AcMarche\Mercredi\Security\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectType extends AbstractType
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected user does not exist',
        ));

        $users = $this->om->getRepository('AcSecurityBundle:User')->getList();

        $resolver->setDefaults(array(
            'required' => false,
            'label' => 'Utilisateur',
            'choices' => $users,
            'placeholder' => 'Choisissez un utilisateur',
        ));
    }

    public function getParent()
    {
        return ChoiceType::class ;
    }
}
