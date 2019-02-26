<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AcMarche\Mercredi\Security\Provider;

class EmailUserProvider extends UserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser($username)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $username)) {
            $user = $this->userRepository->findOneBy(['email' => $username]);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->userRepository->findOneBy(['username' => $username]);
    }
}
