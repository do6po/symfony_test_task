<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 20.02.19
 * Time: 12:24
 */

namespace Tests\AppBundle\Repositories;


use AppBundle\Entity\UserGroup;
use AppBundle\Repository\UserGroupRepository;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserGroupRepositoryTest extends KernelTestCase
{
    use FixtureLoaderTrait;

    /**
     * @var UserGroupRepository
     */
    private $userGroupRepository;

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->userGroupRepository = $this->entityManager->getRepository(UserGroup::class);

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
        $groupArray = [
            'name' => 'Group name 1 new',
        ];

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $groupArray);

        $group = new UserGroup();
        $group->setName($groupArray['name']);

        $this->userGroupRepository->save($group);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $groupArray);
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
        $groupId = 1;
        $groupName = 'Group name 1';

        $data = [
            'id' => $groupId,
            'name' => $groupName,
        ];

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $data);
        /** @var UserGroup $group */
        $group = $this->userGroupRepository->find($groupId);

        $newGroupName = 'Group name 1 edited';

        $group->setName($newGroupName);

        $this->userGroupRepository->save($group);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $groupId,
            'name' => $newGroupName,
        ]);

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $data);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testRemoveUser()
    {
        $groupId = 2;
        /** @var UserGroup $group */
        $group = $this->userGroupRepository->find($groupId);

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, [
            'id' => $groupId,
        ]);

        $this->assertDatabaseHas('users_groups', [
            'user_group_id' => $groupId,
        ]);

        $this->userGroupRepository->remove($group);

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, [
            'id' => $groupId
        ]);

        $this->assertDatabaseMissing('users_groups', [
            'user_group_id' => $groupId,
        ]);
    }
}