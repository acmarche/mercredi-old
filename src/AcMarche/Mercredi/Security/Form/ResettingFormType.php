<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/07/18
 * Time: 13:45.
 */

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'first_options' => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Répéter le mot de passe'],
            'invalid_message' => 'Les mots de passe doivent correspondre',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_token_id' => 'resetting',
        ]);
    }
}
