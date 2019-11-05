<?php

namespace AcMarche\Mercredi\Admin\Form\Animateur;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimateurEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tailles = EnfanceData::getTailleTshirt();
        $groupes = ScolaireService::getGroupesScolaires();

        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
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
                [
                    'label' => 'Né le',
                    'widget' => 'text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => ['class' => 'birthday-text'],
                ]
            )
            ->add('numero_national')
            ->add('num_assimilation')
            ->add('num_bancaire')
            ->add(
                'diplome',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 4],
                ]
            )
            ->add(
                'groupe_souhaite',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $groupes,
                    'placeholder' => 'Choisissez un groupe',
                ]
            )
            ->add(
                'taille_tshirt',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $tailles,
                    'placeholder' => 'Choisissez une taille',
                ]
            )
            ->add(
                'disponibilite',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 4],
                ]
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 4],
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Curriculum vitae',
                    'required' => false,
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => "Photo de l'animateur",
                    'required' => false,
                ]
            )
            ->add(
                'diplome_file',
                FileType::class,
                [
                    'label' => 'Copie du diplôme',
                    'required' => false,
                ]
            )
            ->add(
                'certificat',
                FileType::class,
                [
                    'label' => 'Certificat de capacité de travail',
                    'required' => false,
                ]
            )
            ->add(
                'casier',
                FileType::class,
                [
                    'label' => 'Certificat de bonne vie et moeurs',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Animateur::class,
            ]
        );
    }

    public function getParent()
    {
        return AnimateurType::class;
    }
}
