<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 15.03.19
 * Time: 12:55
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\UserGroup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\WebTestCase;

class UserGroupControllerTest extends WebTestCase
{
    use FixtureLoaderTrait;

    /**
     * @var Client
     */
    private $client;

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->client = static::createClient();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->client = null;
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
        $data = [
            'name' => 'newUserGroup',
        ];

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $data);

        $this->client->request(
            'POST',
            '/api/groups',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $this->client->getResponse();
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
     * @param $request
     * @param $expectedCode
     * @param $expectedResponseContent
     * @dataProvider createWithValidationDataProvider
     */
    public function testCreateWithValidation($request, $expectedCode, $expectedResponseContent)
    {
        $this->client->request(
            'POST',
            '/api/groups',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($request)
        );

        $response = $this->client->getResponse();

        $this->assertEquals($expectedCode, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonString(json_encode($expectedResponseContent), $response->getContent());
    }

    public function createWithValidationDataProvider()
    {
        return [
            [
                [
                    'name' => 'Group name 5',
                ],
                200,
                [
                    'id' => 5,
                    'name' => 'Group name 5',
                ],
            ],
            [
                [
                    'name' => 'Group name 1',
                ],
                422,
                [
                    'name' => ['This value is already used.']
                ],
            ],
            [
                [
                    'name' => '',
                ],
                422,
                [
                    'name' => ['This value should not be blank.']
                ],

            ],
            [
                [
                    'name' => 'Gr',
                ],
                422,
                [
                    'name' => ['This value is too short. It should have 3 characters or more.']
                ],
            ]
        ];
    }

    /**
     * @param $request
     * @param $expectedCode
     * @param $expectedResponseContent
     * @dataProvider editWithValidationDataProvider
     */
    public function testEditWithValidation($request, $expectedCode, $expectedResponseContent)
    {
        $this->client->request(
            'PUT',
            sprintf('/api/groups/%s', $request['id']),
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($request)
        );

        $response = $this->client->getResponse();

        $this->assertEquals($expectedCode, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonString(json_encode($expectedResponseContent), $response->getContent());
    }

    public function editWithValidationDataProvider()
    {
        return [
            [
                [
                    'id' => 1,
                    'name' => 'Group name 1 edited'
                ],
                200,
                [
                    'id' => 1,
                    'name' => 'Group name 1 edited'
                ],
            ],
            [
                [
                    'id' => 1,
                    'name' => 'Group name 1'
                ],
                200,
                [
                    'id' => 1,
                    'name' => 'Group name 1'
                ],
            ],
            [
                [
                    'id' => 1,
                    'name' => ''
                ],
                422,
                [
                    'name' => ['This value should not be blank.']
                ],
            ],
            [
                [
                    'id' => 1,
                    'name' => ''
                ],
                422,
                [
                    'name' => ['This value should not be blank.']
                ],
            ],
            [
                [
                    'id' => 1,
                    'name' => 'Group name 2'
                ],
                422,
                [
                    'name' => ['This value is already used.']
                ],
            ],
            [
                [
                    'id' => 999,
                    'name' => 'Group name 2'
                ],
                404,
                [
                    'message' => 'Not Found!',
                ],
            ],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testEdit()
    {
        $data = [
            'id' => 1,
            'name' => 'Group name 1 edit',
        ];

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $data);

        $this->client->request(
            'PUT',
            sprintf('/api/groups/%s', $data['id']),
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $this->client->getResponse();
        $this->assertJsonStringEqualsJsonString(json_encode($data), $response->getContent());

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $data);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testDelete()
    {
        $data = [
            'id' => 1,
        ];

        $this->assertDatabaseHas(UserGroup::TABLE_NAME, $data);

        $this->client->request(
            'DELETE',
            sprintf('/api/groups/%s', $data['id']),
            [], [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $data);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testDeleteForNotExist()
    {
        $data = [
            'id' => 999,
        ];

        $this->assertDatabaseMissing(UserGroup::TABLE_NAME, $data);

        $this->client->request(
            'DELETE',
            sprintf('/api/groups/%s', $data['id']),
            [], [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'message' => 'Not Found!',
        ]), $response->getContent());
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testAddToGroup()
    {
        $userId = 1;
        $groupId = 3;

        $this->assertDatabaseMissing('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);

        $this->client->request(
            'PUT',
            sprintf('/api/groups/%s/add', $groupId),
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'user_id' => $userId,
            ])
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(json_encode(['added' => true]), $response->getContent());

        $this->assertDatabaseHas('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testDelFromGroup()
    {
        $userId = 1;
        $groupId = 1;

        $this->assertDatabaseHas('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);

        $this->client->request(
            'DELETE',
            sprintf('/api/groups/%s/del', $groupId),
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'user_id' => $userId,
            ])
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(json_encode(['deleted' => true]), $response->getContent());

        $this->assertDatabaseMissing('users_groups', [
            'user_id' => $userId,
            'user_group_id' => $groupId,
        ]);
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            sprintf('/api/groups'),
            [], [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonString(json_encode([
            [
                'id' => 1,
                'name' => 'Group name 1',
            ],
            [
                'id' => 2,
                'name' => 'Group name 2',
            ],
            [
                'id' => 3,
                'name' => 'Group name 3',
            ],
            [
                'id' => 4,
                'name' => 'Group name 4',
            ],
        ]), $response->getContent());
    }

    public function testShow()
    {
        $groupId = 2;
        $this->client->request(
            'GET',
            sprintf('/api/groups/%s', $groupId),
            [], [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonString(json_encode([
            [
                'id' => 1,
                'name' => 'Username1',
                'email' => 'username1@email.com',
            ],
            [
                'id' => 2,
                'name' => 'Username2',
                'email' => 'username2@email.com',
            ],
            [
                'id' => 3,
                'name' => 'Username3',
                'email' => 'username3@email.com',
            ],
        ]), $response->getContent());
    }
}