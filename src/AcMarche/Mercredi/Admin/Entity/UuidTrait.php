<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 5/03/18
 * Time: 10:54
 */

namespace AcMarche\Mercredi\Admin\Entity;

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

    /**
     * @return \Ramsey\Uuid\Uuid
     */
    public function getUuid(): \Ramsey\Uuid\Uuid
    {
        return $this->uuid;
    }

    /**
     * @param \Ramsey\Uuid\Uuid $uuid
     */
    public function setUuid(\Ramsey\Uuid\Uuid $uuid): void
    {
        $this->uuid = $uuid;
    }

}