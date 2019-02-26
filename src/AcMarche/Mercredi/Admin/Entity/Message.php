<?php

namespace AcMarche\Mercredi\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("message")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\MessageRepository")
 */
class Message
{
    use TimestampableEntity;

    /**
     * @var integer|null $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null $from
     * @Assert\NotBlank()
     */
    protected $from;

    /**
     * @var string|null $sujet
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $sujet;

    /**
     * @var string|null $texte
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $texte;

    /**
     * @var UploadedFile|null $file
     */
    protected $file;

    /**
     * @var array|null $destinataires
     *
     * @ORM\Column(type="array", nullable=false)
     *
     */
    protected $destinataires;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @param null|string $from
     */
    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    /**
     * @return null|string
     */
    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    /**
     * @param null|string $sujet
     */
    public function setSujet(?string $sujet): void
    {
        $this->sujet = $sujet;
    }

    /**
     * @return null|string
     */
    public function getTexte(): ?string
    {
        return $this->texte;
    }

    /**
     * @param null|string $texte
     */
    public function setTexte(?string $texte): void
    {
        $this->texte = $texte;
    }

    /**
     * @return null|UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param null|UploadedFile $file
     */
    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }

    /**
     * @return array|null
     */
    public function getDestinataires(): ?array
    {
        return $this->destinataires;
    }

    /**
     * @param array|null $destinataires
     */
    public function setDestinataires(?array $destinataires): void
    {
        $this->destinataires = $destinataires;
    }
}
