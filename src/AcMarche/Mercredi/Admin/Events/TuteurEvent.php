<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:27
 */

namespace AcMarche\Mercredi\Admin\Events;

use AcMarche\Mercredi\Logger\Events\AbstractLoggerEvent;

class TuteurEvent extends AbstractLoggerEvent
{
    const TUTEUR_SHOW = 'tuteur_show';
    const TUTEUR_EDIT = 'tuteur_edit';
}
