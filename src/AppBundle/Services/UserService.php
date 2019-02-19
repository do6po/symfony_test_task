<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 12.02.19
 * Time: 19:04
 */

namespace AppBundle\Services;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
     * @param User $user
     */
    public function save(User $user)
    {
        $this->userRepository->save($user);
    }

    /**
     * @param User $user
     */
    public function delete(User $user)
    {
        $this->userRepository->remove($user);
    }
}