<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/01/18
 * Time: 16:11
 */

namespace AcMarche\Mercredi\Logger\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Logger\Repository\LogRepository")
 * @ORM\Table(name="log")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @ORM\Column(name="context", type="array")
     */
    private $context;

    /**
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(name="level_name", type="string", length=50)
     */
    private $levelName;

    /**
     * @ORM\Column(name="extra", type="array")
     */
    private $extra;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * STOP
     */

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set context.
     *
     * @param array $context
     *
     * @return Log
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set level.
     *
     * @param int $level
     *
     * @return Log
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set levelName.
     *
     * @param string $levelName
     *
     * @return Log
     */
    public function setLevelName($levelName)
    {
        $this->levelName = $levelName;

        return $this;
    }

    /**
     * Get levelName.
     *
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * Set extra.
     *
     * @param array $extra
     *
     * @return Log
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra.
     *
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Log
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
