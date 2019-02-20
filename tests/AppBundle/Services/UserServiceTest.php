<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 18.02.19
 * Time: 18:15
 */

namespace Tests\AppBundle\Services;


use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Services\UserService;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserServiceTest extends KernelTestCase
{
    use FixtureLoaderTrait;

    /**
     * @var UserService
     */
    private $service;

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->service = $this->container->get('app.user_service');
    }

    public function fixtures(): array
    {
        return [
            UsersGroupsFixture::class
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testAdd()
    {
        $userName = 'NewUserName';
        $userEmail = 'new_user_email@example.com';

        $this->assertDatabaseMissing(User::TABLE_NAME, [
            'name' => $userName,
            'email' => $userEmail
        ]);

        $user = $this->service->add($userName, $userEmail);
        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'name' => $userName,
            'email' => $userEmail
        ]);
    }

    /**
     * @param $userId
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @dataProvider deleteDataProvider
     */
    public function testDelete($userId)
    {
        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId
        ]);

        $user = $this->service->find($userId);

        $this->service->delete($user);

        $this->assertDatabaseMissing(User::TABLE_NAME, [
            'id' => $userId
        ]);
    }

    public function deleteDataProvider()
    {
        return [
            [1],
            [2],
            [3],
            [4],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testEdit()
    {
        $userId = 1;
        $userName = 'Username1';
        $userEmail = 'username1@email.com';

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId,
            'name' => $userName,
            'email' => $userEmail
        ]);

        $editedUserName = 'editedUserName';
        $editedUserEmail = 'edited_user_email@example.com';

        $user = $this->service->edit($userId, $editedUserName, $editedUserEmail);
        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId,
            'name' => $editedUserName,
            'email' => $editedUserEmail
        ]);
    }

    /**
     * @param $groupId
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @dataProvider deleteDataProvider
     */
    public function testDeleteGroup($groupId)
    {
        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $groupId,
        ]);

        $this->assertDatabaseHas('users_groups', [
            'user_group_id' => $groupId,
        ]);

        $group = $this->service->findGroup($groupId);
        $this->assertInstanceOf(UserGroup::class, $group);

        $this->service->deleteGroup($group);

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, [
            'id' => $groupId,
        ]);

        $this->assertDatabaseMissing('users_groups', [
            'user_group_id' => $groupId,
        ]);
    }

    public function deleteGroupDataProvider()
    {
        return [
            [1],
            [2],
            [3],
            [4],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testAddGroup()
    {
        $newGroupName = 'new group';

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, [
            'name' => $newGroupName
        ]);

        $group = $this->service->addGroup($newGroupName);
        $this->assertInstanceOf(UserGroup::class, $group);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'name' => $newGroupName
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testEditGroup()
    {
        $groupId = 1;
        $groupName = 'Group name 1';

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $groupId,
            'name' => $groupName,
        ]);

        $newGroupName = 'Group name new';
        $this->service->editGroup($groupId, $newGroupName);

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, [
            'id' => $groupId,
            'name' => $groupName,
        ]);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $groupId,
            'name' => $newGroupName,
        ]);
    }
}