<?php

namespace AcMarche\Mercredi\Admin\Security;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class EcoleVoter extends Voter
{
    /**
     * @var User $user
     */
    private $user;
    const INDEX = 'index_ecole';
    const SHOW = 'show';
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';
    private $decisionManager;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;


    public function __construct(AccessDecisionManagerInterface $decisionManager, FlashBagInterface $flashBag)
    {
        $this->decisionManager = $decisionManager;
        $this->flashBag = $flashBag;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        //a cause de index pas d'ecole defini
        if ($subject) {
            if(!$subject instanceof Ecole)
                return false;
        }

        return in_array(
            $attribute,
            [self::INDEX, self::SHOW, self::ADD, self::EDIT, self::DELETE]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $enfant, TokenInterface $token)
    {
        $this->user = $token->getUser();

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex($token);
            case self::SHOW:
                return $this->canView($token);
            case self::ADD:
                return $this->canAdd($token);
            case self::EDIT:
                return $this->canEdit($token);
            case self::DELETE:
                return $this->canDelete($token);
        }

        return false;
    }

    private function canIndex(TokenInterface $token)
    {

        if ($this->canEdit($token)) {
            return true;
        }

        if ($this->checkEcoles($token)) {
            return true;
        }

        $this->flashBag->add('danger', 'Aucune école attribué à votre compte');

        return false;
    }

    private function canView(TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_READ'])) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ANIMATEUR'])) {
            return false;
        }


        return $this->checkEcoles($token);
    }

    private function canEdit(TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ADMIN'])) {
            return true;
        }

        return false;
    }

    private function canAdd(TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        return false;
    }

    private function canDelete(TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        return false;
    }

    private function checkEcoles($token)
    {
        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ECOLE'])) {
            $ecoles = $this->user->getEcoles();
            if (count($ecoles) > 0) {
                return true;
            }
        }

        return false;
    }
}
