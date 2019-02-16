<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 14.02.19
 * Time: 17:50
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\UserGroup;
use Tests\AppBundle\Fixtures\UserGroupFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\KernelTestCase;

class UserGroupTest extends KernelTestCase
{
    use FixtureLoaderTrait;

    public function fixtures(): array
    {
        return [
            UserGroupFixture::class,
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

        $repository = $this->entityManager->getRepository(UserGroup::class);

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
}