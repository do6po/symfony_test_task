<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name=User::TABLE_NAME)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements JsonSerializable
{
    const TABLE_NAME = 'users';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var \Doctrine\Common\Collections\Collection|UserGroup[]
     *
     * @ORM\ManyToMany(targetEntity="UserGroup", inversedBy="users", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="users_groups")
     */
    private $userGroups;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->userGroups = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return UserGroup[]|ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->userGroups;
    }

    /**
     * @param UserGroup $userGroup
     * @return $this
     */
    public function addUserGroup(UserGroup $userGroup)
    {
        if ($this->userGroups->contains($userGroup)) {
            return $this;
        }

        $this->userGroups->add($userGroup);
        $userGroup->addUser($this);

        return $this;
    }

    /**
     * @param UserGroup $userGroup
     * @return $this
     */
    public function removeUserGroup(UserGroup $userGroup)
    {
        if (!$this->userGroups->contains($userGroup)) {
            return $this;
        }

        $this->userGroups->removeElement($userGroup);
        $userGroup->removeUser($this);

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}

