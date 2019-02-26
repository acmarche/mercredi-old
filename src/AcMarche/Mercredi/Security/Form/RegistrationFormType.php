<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/07/18
 * Time: 13:43
 */

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = EnfanceData::getTypesCompte();

        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add('email', EmailType::class, array())
            ->add(
                'plainPassword',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'options' => array(
                        'attr' => array(
                            'autocomplete' => 'new-password',
                        ),
                    ),
                    'first_options' => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Répéter le mot de passe'),
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisissez le type de compte',
                    'label' => 'En tant que',
                    'choices' => $types,
                ]
            )
            ->add(
                'accord',
                CheckboxType::class,
                [
                    'label' => 'J\'ai lu et j\'accepte le règlement d\'ordre intérieur téléchargeable dans l\'onglet "modalités pratiques"',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
                'csrf_token_id' => 'registration',
            )
        );
    }
}
