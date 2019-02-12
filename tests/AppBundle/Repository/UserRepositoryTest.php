<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 17:37
 */

namespace Tests\AppBundle\Repository;



use AppBundle\Entity\User;
use Tests\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function testCreate()
    {
        $name = 'userName';
        $email = 'user_email@email.com';

        $this->assertDatabaseMissing(User::class, [
            'name' => $name,
            'email' => $email,
        ]);

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseHas(User::class, [
            'name' => $name,
            'email' => $email,
        ]);
    }
}