<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 17:37
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserTest extends KernelTestCase
{
    use FixtureLoaderTrait;

    /**
     * @var UserRepository
     */
    private $repository;

    public function fixtures(): array
    {
        return [
            UsersGroupsFixture::class,
        ];
    }

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->repository = $this->getUserRepository();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreate1()
    {
        $userArray = [
            'name' => 'userName',
            'email' => 'user_email@email.com',
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseHas(User::TABLE_NAME, $userArray);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
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

        $this->assertDatabaseHas(User::TABLE_NAME, $userData);

        $user = $this->repository->find($userId);

        $newUserName = 'usernameNew';
        $newUserEmail = 'newUsername@email.com';

        $user->setName($newUserName);
        $user->setEmail($newUserEmail);

        $this->entityManager->flush($user);

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId,
            'name' => $newUserName,
            'email' => $newUserEmail,
        ]);

        $this->assertDatabaseMissing(User::TABLE_NAME, $userData);
    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\UniqueConstraintViolationException
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreateFailForUniqueName()
    {
        $userArray = [
            'name' => 'username1',
            'email' => 'user_email@email.com',
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseMissing(User::TABLE_NAME, $userArray);
    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\UniqueConstraintViolationException
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreateFailForUniqueEmail()
    {
        $userArray = [
            'name' => 'username999',
            'email' => 'username1@email.com',
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $userArray);

        $user = new User();
        $user->setName($userArray['name']);
        $user->setEmail($userArray['email']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertDatabaseMissing(User::TABLE_NAME, $userArray);
    }

    /**
     * @param $userId
     * @param $groupCount
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @dataProvider getGroupDataProvider
     */
    public function testGetGroups($userId, $groupCount)
    {
        /** @var User $user */
        $user = $this->repository->find($userId);

        $groups = $user->getGroups();

        $this->assertEquals($groupCount, $groups->count());
    }

    public function getGroupDataProvider()
    {
        return [
            [1, 2],
            [2, 3],
            [3, 4],
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testAddGroup()
    {
        /** @var User $user */
        $userId = 1;
        $user = $this->repository->find($userId);

        $groupId = 3;

        $groupRepository = $this->entityManager->getRepository(UserGroup::class);
        $group = $groupRepository->find($groupId);
    }

    /**
     * @return \AppBundle\Repository\UserRepository|\Doctrine\ORM\EntityRepository
     */
    protected function getUserRepository()
    {
        return $this->entityManager->getRepository(User::class);
    }
}