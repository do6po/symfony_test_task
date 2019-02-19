<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 18.02.19
 * Time: 18:15
 */

namespace Tests\AppBundle\Services;


use AppBundle\Entity\User;
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

    public function testEdit()
    {

    }
}