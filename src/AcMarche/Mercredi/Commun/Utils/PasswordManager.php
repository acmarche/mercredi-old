<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/11/18
 * Time: 12:03
 */

namespace AcMarche\Mercredi\Commun\Utils;

use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordManager
{
    /**
     * @var StringUtils
     */
    private $stringUtils;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        StringUtils $stringUtils,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->stringUtils = $stringUtils;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function generateNewPasswordAndSetPlainPassword(User $user)
    {
        $password = $this->stringUtils->generatePassword();
        $user->setPlainPassword($password);
    }

    public function changePassword(User $user, string $plainPassword)
    {
        $passwordCrypted = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($passwordCrypted);
        $user->setPlainPassword($plainPassword);//pour envoie par mail
    }
}