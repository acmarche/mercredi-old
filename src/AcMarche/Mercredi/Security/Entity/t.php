<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/01/19
 * Time: 19:57
 */

class t
{


    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === 'ROLE_USER') {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }


    public function getRoles()
    {

    }

    /**
     * {@inheritdoc}
     */

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }





    public function setSingleGroup(Group $group = null)
    {
        if (!$group) {
            return;
        }

        $this->groups->add($group);
    }

    public function getSingleGroup()
    {
        return $this->groups->first();
    }

    /**
     * @return Animateur|null
     */
    public function getAnimateur(): ?Animateur
    {
        return $this->animateur;
    }

    /**
     * @param Animateur|null $animateur
     */
    public function setAnimateur(?Animateur $animateur): void
    {
        $this->animateur = $animateur;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiToken(): ?string
    {
        return $this->api_token;
    }

    public function setApiToken(?string $api_token): self
    {
        $this->api_token = $api_token;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (!$this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        if ($this->ecoles->contains($ecole)) {
            $this->ecoles->removeElement($ecole);
        }

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }
}