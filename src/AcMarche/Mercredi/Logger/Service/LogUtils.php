<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 14:48
 */

namespace AcMarche\Mercredi\Logger\Service;

use AcMarche\Mercredi\Logger\Entity\Log;

class LogUtils
{
    public function getUser(Log $log)
    {
        $extra = $log->getExtra();
        $user = isset($extra['user']) ? $extra['user'] : null;

        return $user;
    }
}
