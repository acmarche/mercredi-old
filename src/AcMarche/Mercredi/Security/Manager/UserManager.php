<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/08/18
 * Time: 13:17
 */

namespace AcMarche\Mercredi\Security\Manager;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Entity\UserPopulateInterface;
use AcMarche\Mercredi\Commun\Utils\PasswordManager;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Repository\GroupRepository;
use AcMarche\Mercredi\Security\Repository\UserRepository;

class UserManager
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var PasswordManager
     */
    private $passwordManager;

    public function __construct(
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        PasswordManager $passwordManager
    ) {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->passwordManager = $passwordManager;
    }

    public function getInstance(string $email): User
    {
        if (!$user = $this->findOneByEmail($email)) {
            $user = new User();
            $user->setEmail($email);
            $user->setUsername($email);
        }

        $user->setEnabled(true);

        return $user;
    }

    public function newFromAnimateur(Animateur $animateur, User $user = null): User
    {
        if (!$user) {
            $user = $this->getInstance($animateur->getEmail());
            $this->populateFromObject($user, $animateur);
        }
        $this->addGroupByDefault($user, $animateur->getRoleByDefault());
        $this->associateAnimateurAndUser($user, $animateur);
        $this->passwordManager->generateNewPasswordAndSetPlainPassword($user);
        $this->passwordManager->changePassword($user, $user->getPlainPassword());
        $this->userRepository->insert($user);

        return $user;
    }

    public function newFromTuteur(Tuteur $tuteur, User $user = null): User
    {
        if (!$user) {
            $user = $this->getInstance($tuteur->getEmail());
            $this->populateFromObject($user, $tuteur);
        }
        $this->addGroupByDefault($user, $tuteur->getRoleByDefault());
        $this->associateTuteurAndUser($user, $tuteur);
        $this->passwordManager->generateNewPasswordAndSetPlainPassword($user);
        $this->passwordManager->changePassword($user, $user->getPlainPassword());
        $this->userRepository->insert($user);

        return $user;
    }

    public function populateFromObject(User $user, UserPopulateInterface $object)
    {
        $user->setNom($object->getNom());
        $user->setPrenom($object->getPreNom());
        if ($object->getEmail()) {
            $user->setEmail($object->getEmail());
        }
        $this->addGroupByDefault($user, $object->getRoleByDefault());
    }

    public function insert(User $user)
    {
        $user->setEmail($user->getEmail());
        $user->setUsername($user->getEmail());//pour setUsername();
        $this->passwordManager->generateNewPasswordAndSetPlainPassword($user);
        $this->passwordManager->changePassword($user, $user->getPlainPassword());
        $this->userRepository->insert($user);
    }

    public function save()
    {
        $this->userRepository->save();
    }

    public function delete(User $user)
    {
        $this->userRepository->remove($user);
    }

    public function associateTuteurAndUser(User $user, Tuteur $tuteur)
    {
        $user->setTuteur($tuteur);
        $tuteur->setUser($user);
    }

    public function associateAnimateurAndUser(User $user, Animateur $animateur)
    {
        $user->setAnimateur($animateur);
        $animateur->setUser($user);
    }

    public function addGroupByDefault(User $user, string $name)
    {
        $group = $this->groupRepository->findOneBy(['name' => $name]);
        if ($group) {
            if (!$user->hasRole($name)) {
                $user->addGroup($group);
            }
        }
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email)
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function getRolesForProfile(User $user): iterable
    {
        $roles = $user->getRoles();
        if ($user->hasRole('ROLE_MERCREDI_ADMIN')) {
            if (($key = array_search('ROLE_MERCREDI_READ', $roles)) !== false) {
                unset($roles[$key]);
            }
        }

        return $roles;
    }

}