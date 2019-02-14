<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 17:37
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\User;
use Tests\AppBundle\Fixtures\UserFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserTest extends KernelTestCase
{
    use FixtureLoaderTrait;

    public function fixtures(): array
    {
        return [
            UserFixture::class,
        ];
    }

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function testCreate()
    {
        $userArray = [
            'name' => 'userName',
            'email' => 'user_email@email.com',
        ];

        $this->assertDatabaseMissing(User::class, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseHas(User::class, $userArray);
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \ReflectionException
     */
    public function testUpdate()
    {
        $userId = 1;
        $userName = 'username1';
        $userEmail = 'username1@email.com';

        $userData = [
            'id' => $userId,
            'name' => $userName,
            'email' => $userEmail,
        ];

        $this->assertDatabaseHas(User::class, $userData);

        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->find($userId);

        $newUserName = 'usernameNew';
        $newUserEmail = 'newUsername@email.com';

        $user->setName($newUserName);
        $user->setEmail($newUserEmail);

        $this->entityManager->flush($user);

        $this->assertDatabaseHas(User::class, [
            'id' => $userId,
            'name' => $newUserName,
            'email' => $newUserEmail,
        ]);

        $this->assertDatabaseMissing(User::class, $userData);
    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\UniqueConstraintViolationException
     *
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateFailForUniqueName()
    {
        $userArray = [
            'name' => 'username1',
            'email' => 'user_email@email.com',
        ];

        $this->assertDatabaseMissing(User::class, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseMissing(User::class, $userArray);
    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\UniqueConstraintViolationException
     *
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateFailForUniqueEmail()
    {
        $userArray = [
            'name' => 'username999',
            'email' => 'username1@email.com',
        ];

        $this->assertDatabaseMissing(User::class, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseMissing(User::class, $userArray);
    }
}