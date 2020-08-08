<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 5/03/18
 * Time: 10:54.
 */

namespace AcMarche\Mercredi\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

trait UuidTrait
{
    /**
     * @var \Ramsey\Uuid\Uuid
     *
     * ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $uuid;

    public function getUuid(): \Ramsey\Uuid\UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(\Ramsey\Uuid\Uuid $uuid): void
    {
        $this->uuid = $uuid;
    }
}
