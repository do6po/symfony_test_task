<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 12.02.19
 * Time: 19:04
 */

namespace AppBundle\Services;


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

    public function add()
    {

    }
}