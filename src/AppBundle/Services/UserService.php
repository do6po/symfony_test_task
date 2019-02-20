<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 12.02.19
 * Time: 19:04
 */

namespace AppBundle\Services;


use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Repository\UserGroupRepository;
use AppBundle\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserGroupRepository
     */
    private $groupRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     * @param UserGroupRepository $groupRepository
     */
    public function __construct(UserRepository $userRepository, UserGroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param int $id
     * @return User|null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function find(int $id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param int $id
     * @return UserGroup|null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function findGroup(int $id)
    {
        return $this->groupRepository->find($id);
    }

    /**
     * @param string $name
     * @param string $email
     * @return User
     */
    public function add(string $name, string $email)
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);

        $this->save($user);

        return $user;
    }

    /**
     * @param string $name
     * @return UserGroup
     */
    public function addGroup(string $name): UserGroup
    {
        $group = new UserGroup();
        $group->setName($name);

        $this->saveGroup($group);

        return $group;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $email
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function edit(int $id, string $name, string $email): User
    {
        $user = $this->findOrFail($id);

        $user->setName($name);
        $user->setEmail($email);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param int $id
     * @param string $name
     * @return UserGroup
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function editGroup(int $id, string $name): UserGroup
    {
        $group = $this->findGroupOrFail($id);

        $group->setName($name);

        $this->groupRepository->save($group);

        return $group;
    }

    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function saveGroup(UserGroup $group): void
    {
        $this->groupRepository->save($group);
    }

    public function delete(User $user): void
    {
        $this->userRepository->remove($user);
    }

    public function deleteGroup($group): void
    {
        $this->groupRepository->remove($group);
    }

    /**
     * @param int $id
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function findOrFail(int $id)
    {
        /** @var User $user */
        if (($user = $this->userRepository->find($id)) !== null) {
            return $user;
        }

        throw new NotFoundHttpException(sprintf('User with id: %s - not found!', $id));
    }

    /**
     * @param int $id
     * @return UserGroup
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function findGroupOrFail(int $id)
    {
        /** @var UserGroup $group */
        if (($group = $this->groupRepository->find($id)) !== null) {
            return $group;
        }

        throw new NotFoundHttpException(sprintf('Group with id: %s - not found!', $id));
    }
}