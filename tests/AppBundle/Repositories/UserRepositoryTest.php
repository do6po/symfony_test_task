<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 19.02.19
 * Time: 18:08
 */

namespace Tests\AppBundle\Repositories;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixtureLoaderTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function fixtures(): array
    {
        return [
            UsersGroupsFixture::class,
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testSaveForNewUser()
    {
        $userArray = [
            'name' => 'userName',
            'email' => 'user_email@email.com',
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->userRepository->save($user);

        $this->assertDatabaseHas(User::TABLE_NAME, $userArray);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testSaveForUpdatedUser()
    {
        $userId = 1;
        $userName = 'username1';
        $userEmail = 'username1@email.com';

        $userData = [
            'id' => $userId,
            'name' => $userName,
            'email' => $userEmail,
        ];

        $this->assertDatabaseHas(User::TABLE_NAME, $userData);
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        $newUserName = 'usernameNew';
        $newUserEmail = 'newUsername@email.com';

        $user->setName($newUserName);
        $user->setEmail($newUserEmail);

        $this->userRepository->save($user);

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId,
            'name' => $newUserName,
            'email' => $newUserEmail,
        ]);

        $this->assertDatabaseMissing(User::TABLE_NAME, $userData);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testRemoveUser()
    {
        $userId = 2;
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId,
        ]);

        $this->assertDatabaseHas('users_groups', [
            'user_id' => $userId,
        ]);

        $this->userRepository->remove($user);

        $this->assertDatabaseMissing(User::TABLE_NAME, [
            'id' => $userId
        ]);

        $this->assertDatabaseMissing('users_groups', [
            'user_id' => $userId,
        ]);
    }
}