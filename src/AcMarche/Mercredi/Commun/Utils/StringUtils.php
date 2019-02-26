<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/08/18
 * Time: 17:05
 */

namespace AcMarche\Mercredi\Commun\Utils;


class StringUtils
{
    public function generatePassword()
    {
        $password = '';

        for ($i = 0; $i < 6; $i++) {
            $password .= rand(1, 9);
        }

        return $password;
    }
}