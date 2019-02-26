<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/11/18
 * Time: 9:44
 */

namespace AcMarche\Mercredi\Admin\Entity;


interface UserPopulateInterface
{
    public function getNom(): ?string;

    public function setNom(string $nom): self;

    public function getPrenom(): ?string;

    public function setPrenom(string $prenom): self;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    public function getRoleByDefault(): string;

}