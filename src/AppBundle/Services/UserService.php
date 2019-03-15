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
use AppBundle\Exceptions\NotFoundHttpException;
use AppBundle\Repository\UserGroupRepository;
use AppBundle\Repository\UserRepository;

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

    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function findGroupsAll(): array
    {
        return $this->groupRepository->findAll();
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
     * @param $user
     * @return mixed
     */
    public function add($user)
    {
        $this->save($user);

        return $user;
    }

    /**
     * @param UserGroup $group
     * @return UserGroup
     */
    public function addGroup(UserGroup $group): UserGroup
    {
        $this->saveGroup($group);

        return $group;
    }

    /**
     * @param $user
     * @return User
     */
    public function edit($user): User
    {
        $this->save($user);

        return $user;
    }

    /**
     * @param UserGroup $group
     * @return UserGroup
     */
    public function editGroup(UserGroup $group): UserGroup
    {
        $this->saveGroup($group);

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

    /**
     * @param User $user
     */
    public function delete(User $user): void
    {
        $this->userRepository->remove($user);
    }

    /**
     * @param UserGroup $group
     */
    public function deleteGroup(UserGroup $group): void
    {
        $this->groupRepository->remove($group);
    }

    /**
     * @param int $id
     * @return User
     * @throws NotFoundHttpException
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

        throw new NotFoundHttpException([
            'error' => sprintf('User with id: %s - not found!', $id),
        ]);
    }

    /**
     * @param int $id
     * @return UserGroup
     * @throws NotFoundHttpException
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

        throw new NotFoundHttpException([
            'error' => sprintf('Group with id: %s - not found!', $id),
        ]);
    }
}