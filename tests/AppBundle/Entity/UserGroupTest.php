<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 14.02.19
 * Time: 17:50
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserGroupTest extends KernelTestCase
{
    use FixtureLoaderTrait;

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
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreate()
    {
        $groupArray = [
            'name' => 'New Group 5',
        ];

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $groupArray);

        $group = new UserGroup();
        $group->setName($groupArray['name']);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $groupArray);
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
        $id = 1;
        $oldName = 'Group name 1';

        $groupArray = [
            'id' => $id,
            'name' => $oldName
        ];

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $groupArray);

        $repository = $this->getGroupRepository();

        /** @var UserGroup $group */
        $group = $repository->find($groupArray['id']);

        $newName = $oldName . ' updated';

        $group->setName($newName);

        $this->entityManager->flush($group);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $id,
            'name' => $newName
        ]);

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $groupArray);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testAddUser()
    {
        $userId = 1;
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        $groupId = 3;
        /** @var UserGroup $group */
        $group = $this->getGroupRepository()->find($groupId);

        $this->assertDatabaseMissing('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);

        $group->addUser($user);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $this->assertDatabaseHas('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testRemoveUser()
    {
        $userId = 2;
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        $groupId = 2;
        /** @var UserGroup $group */
        $group = $this->getGroupRepository()->find($groupId);

        $this->assertDatabaseHas('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);

        $group->removeUser($user);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $this->assertDatabaseMissing('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testRemoveGroup()
    {
        $groupId = 2;
        /** @var UserGroup $group */
        $group = $this->getGroupRepository()->find($groupId);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $groupId,
        ]);

        $this->assertDatabaseHas('users_groups', [
            'user_group_id' => $groupId,
        ]);

        $this->entityManager->remove($group);
        $this->entityManager->flush();

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, [
            'id' => $groupId
        ]);

        $this->assertDatabaseMissing('users_groups', [
            'user_group_id' => $groupId,
        ]);
    }

    /**
     * @return \AppBundle\Repository\UserGroupRepository|\Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    protected function getGroupRepository()
    {
        return $this->entityManager->getRepository(UserGroup::class);
    }
}