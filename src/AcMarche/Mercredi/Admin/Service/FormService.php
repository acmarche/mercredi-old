<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/08/18
 * Time: 16:51
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Form\Enfant\EnfantSetTuteurType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

class FormService
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param EnfantTuteur $enfant_tuteur
     * @return FormInterface
     */
    public function createAttachForm(EnfantTuteur $enfant_tuteur)
    {
        $enfant = $enfant_tuteur->getEnfant();

        $form = $this->formFactory->create(
            EnfantSetTuteurType::class,
            $enfant_tuteur,
            array(
                'action' => $this->router->generate('tuteur_attach', array('id' => $enfant->getId())),
            )
        );
        $form->add('submit', SubmitType::class, array('label' => 'Définir comme parent'));

        return $form;
    }

    /**
     * @param Enfant $enfant
     * @return FormInterface
     */
    public function createDetachForm(Enfant $enfant)
    {
        $builder = $this->formFactory->createBuilder();
        $form = $builder
            ->setAction(
                $this->router->generate(
                    'tuteur_detach',
                    array(
                        'id' => $enfant->getId(),
                    )
                )
            )
            ->add('tuteur_id', HiddenType::class)
            ->add('submit', SubmitType::class, array('label' => 'Détacher', 'attr' => array('class' => 'btn-warning')))
            ->getForm();

        return $form;
    }

    /**
     * @param Enfant $enfant
     * @return FormInterface
     */
    public function createDeletePresencesForm(Enfant $enfant)
    {
        $builder = $this->formFactory->createBuilder();
        return $builder
            ->setAction($this->router->generate('presences_delete', array('id' => $enfant->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Supprimer les présences sélectionnées',
                    'attr' => array('class' => 'btn-danger btn-xs'),
                )
            )
            ->getForm();
    }
}