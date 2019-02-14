<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 13.02.19
 * Time: 19:51
 */

namespace AppBundle\Factories;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserRepositoryFactory
{
    /**
     * @param EntityManager $entityManager
     * @return UserRepository
     */
    public static function create(EntityManager $entityManager): UserRepository
    {
        return $entityManager->getRepository(User::class);
    }
}