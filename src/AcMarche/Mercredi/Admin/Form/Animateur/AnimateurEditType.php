<?php

namespace AcMarche\Mercredi\Admin\Form\Animateur;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AcMarche\Mercredi\Admin\Service\EnfanceData;

class AnimateurEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tailles = EnfanceData::getTailleTshirt();
        $groupes = ScolaireService::getGroupesScolaires();

        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true
                ]
            )
            ->add('adresse')
            ->add('code_postal')
            ->add('localite')
            ->add('telephone')
            ->add('gsm')
            ->add(
                'birthday',
                DateType::class,
                array(
                    'label' => "Né le",
                    'widget' => 'text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array('class' => 'birthday-text'),
                )
            )
            ->add('numero_national')
            ->add('num_assimilation')
            ->add('num_bancaire')
            ->add(
                'diplome',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 4),
                )
            )
            ->add(
                'groupe_souhaite',
                ChoiceType::class,
                array(
                    'required' => false,
                    'choices' => $groupes,
                    'placeholder' => 'Choisissez un groupe',
                )
            )
            ->add(
                'taille_tshirt',
                ChoiceType::class,
                array(
                    'required' => false,
                    'choices' => $tailles,
                    'placeholder' => 'Choisissez une taille',
                )
            )
            ->add(
                'disponibilite',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 4),
                )
            )
            ->add(
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 4),
                )
            )
            ->add(
                'file',
                FileType::class,
                array(
                    'label' => "Curriculum vitae",
                    'required' => false,
                )
            )
            ->add(
                'image',
                FileType::class,
                array(
                    'label' => "Photo de l'animateur",
                    'required' => false,
                )
            )
            ->add(
                'diplome_file',
                FileType::class,
                array(
                    'label' => "Copie du diplôme",
                    'required' => false,
                )
            )
            ->add(
                'certificat',
                FileType::class,
                array(
                    'label' => "Certificat de capacité de travail",
                    'required' => false,
                )
            )
            ->add(
                'casier',
                FileType::class,
                array(
                    'label' => "Certificat de bonne vie et moeurs",
                    'required' => false,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Animateur::class,
            )
        );
    }

    public function getParent()
    {
        return AnimateurType::class;
    }


}
