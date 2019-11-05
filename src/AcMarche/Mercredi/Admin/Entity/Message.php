<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Admin\Doctrine\TimestampableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("message")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\MessageRepository")
 */
class Message
{
    use TimestampableEntityTrait;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    protected $from;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $sujet;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $texte;

    /**
     * @var UploadedFile|null
     */
    protected $file;

    /**
     * @var array|null
     *
     * @ORM\Column(type="array", nullable=false)
     */
    protected $destinataires;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

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

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

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
