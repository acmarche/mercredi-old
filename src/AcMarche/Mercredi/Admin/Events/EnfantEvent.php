<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:27
 */

namespace AcMarche\Mercredi\Admin\Events;

use AcMarche\Mercredi\Logger\Events\AbstractLoggerEvent;

class EnfantEvent extends AbstractLoggerEvent
{
    const ENFANT_SHOW = 'enfant_show';
    const ENFANT_EDIT = 'enfant_edit';
    const ENFANT_DOWNLOAD = 'enfant_download';

    protected $type;

    public function __construct($entity = null, $type = null)
    {
        $this->type = $type;
        parent::__construct($entity);
    }

    public function getTypeDownload()
    {
        return $this->type;
    }
}
