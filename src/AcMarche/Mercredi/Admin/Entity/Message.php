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

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
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

    public function getDestinataires(): ?array
    {
        return $this->destinataires;
    }

    public function setDestinataires(array $destinataires): self
    {
        $this->destinataires = $destinataires;

        return $this;
    }
}
