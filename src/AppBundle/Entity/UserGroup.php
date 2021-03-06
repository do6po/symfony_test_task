<?php

namespace AppBundle\Entity;

use AppBundle\Interfaces\FillableFromRequestInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserGroup
 *
 * @ORM\Table(name=UserGroup::TABLE_NAME)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserGroupRepository")
 *
 * @UniqueEntity(fields={"name"})
 *
 */
class UserGroup implements \JsonSerializable, FillableFromRequestInterface
{
    const TABLE_NAME = 'user_groups';

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


    //TODO Не работает сортировка
    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userGroups")
     */
    private $users;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return UserGroup
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
     * @return User[]|ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function users()
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return $this;
        }

        $this->users->add($user);
        $user->addUserGroup($this);

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user)
    {
        if (!$this->users->contains($user)) {
            return $this;
        }

        $this->users->removeElement($user);
        $user->removeUserGroup($this);

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function fillByRequest(Request $request)
    {
        $this->name = $request->get('name');

        return $this;
    }
}

