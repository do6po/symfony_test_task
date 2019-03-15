<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 15.03.19
 * Time: 12:55
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\UserGroup;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\WebTestCase;

class UserGroupControllerTest extends WebTestCase
{
    use FixtureLoaderTrait;

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();
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
    public function testCreate()
    {
        $client = static::createClient();

        $data = [
            'name' => 'newUserGroup',
        ];

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $data);

        $client->request(
            'POST',
            '/api/groups',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'id' => 5,
                'name' => 'newUserGroup',
            ]),
            $response->getContent()
        );

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $data);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testCreateForUnique()
    {
        $client = static::createClient();

        $data = [
            'name' => 'Group name 1',
        ];

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $data);

        $client->request(
            'POST',
            '/api/groups',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'name' => ['This value is already used.']
            ]),
            $response->getContent()
        );
    }
}