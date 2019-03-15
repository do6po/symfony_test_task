<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 12:58
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\WebTestCase;

class UserControllerTest extends WebTestCase
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

    public function testList()
    {
        $this->client->request('GET', '/api/users');

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->assertEquals(7, count(json_decode($response->getContent())));
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testCreateTypeJson()
    {
        $data = [
            'name' => 'newUsername',
            'email' => 'new.email@example.com'
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $data);

        $this->client->request(
            'POST',
            '/api/users',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $this->client->getResponse();
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'id' => 8,
                'name' => 'newUsername',
                'email' => 'new.email@example.com',
            ]),
            $response->getContent()
        );

        $this->assertDatabaseHas(User::TABLE_NAME, $data);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testCreate()
    {
        $data = [
            'name' => 'newUsername',
            'email' => 'new.email@example.com'
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $data);

        $this->client->request('POST', '/api/users', $data);

        $response = $this->client->getResponse();
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'id' => 8,
                'name' => 'newUsername',
                'email' => 'new.email@example.com',
            ]),
            $response->getContent()
        );

        $this->assertDatabaseHas(User::TABLE_NAME, $data);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testEdit()
    {
        $id = 1;

        $oldData = [
            'id' => $id,
            'name' => 'Username1',
            'email' => 'username1@email.com',
        ];

        $newData = [
            'id' => $id,
            'name' => 'editedName',
            'email' => 'edited.email@example.com'
        ];

        $this->assertDatabaseHas(User::TABLE_NAME, $oldData);
        $this->assertDatabaseMissing(User::TABLE_NAME, $newData);

        $this->client->request('PUT', sprintf('/api/users/%s', $id), $newData);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($newData), $response->getContent());

        $this->assertDatabaseMissing(User::TABLE_NAME, $oldData);
        $this->assertDatabaseHas(User::TABLE_NAME, $newData);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testDelete()
    {
        $userId = 1;

        $this->assertDatabaseHas(User::TABLE_NAME, [
            'id' => $userId,
        ]);

        $this->client->request('DELETE', sprintf('/api/users/%s', $userId));

        $this->assertDatabaseMissing(User::TABLE_NAME, [
            'id' => $userId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function testDeleteNotFound()
    {
        $userId = 999;

        $this->assertDatabaseMissing(User::TABLE_NAME, [
            'id' => $userId,
        ]);

        $this->client->request('DELETE', sprintf('/api/users/%s', $userId));

        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'message' => 'Not Found!',
        ]), $response->getContent());
    }

    /**
     * @param $requestData
     * @param $expectedStatus
     * @param $expectedContent
     *
     * @dataProvider createWithValidation
     */
    public function testCreateWithValidation($requestData, $expectedStatus, $expectedContent)
    {
        $this->client->request('POST', sprintf('/api/users'), $requestData);

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertEquals($expectedStatus, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($expectedContent), $content);
    }

    public function createWithValidation()
    {
        return [
            'Empty fields' => [
                [

                    'name' => '',
                    'email' => ''
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    'name' => [
                        'This value should not be blank.',
                    ],
                    'email' => [
                        'This value should not be blank.',
                    ],
                ]
            ],
            [
                [

                    'name' => 'na',
                    'email' => 'mail'
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    'name' => [
                        'This value is too short. It should have 3 characters or more.',
                    ],
                    'email' => ['This value is not a valid email address.'],
                ]
            ],
            [
                [
                    'name' => 'newName',
                    'email' => 'mail@example.com'
                ],
                Response::HTTP_OK,
                [
                    'id' => 8,
                    'name' => 'newName',
                    'email' => 'mail@example.com'
                ]
            ],
            [
                [

                    'name' => 'Username1',
                    'email' => 'email@example.com'
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    'name' => [
                        'This value is already used.',
                    ],
                ]
            ],
            [
                [

                    'name' => 'Username99',
                    'email' => 'username1@email.com'
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [
                    'email' => [
                        'This value is already used.',
                    ],
                ]
            ],
        ];
    }

    /**
     * @param $requestData
     * @param $expectedStatus
     * @param $expectedContent
     *
     * @dataProvider editWithValidation
     */
    public function testEditWithValidation($requestData, $expectedStatus, $expectedContent)
    {
        $this->client->request('PUT', sprintf('/api/users/%s', $requestData['id']), $requestData);

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertEquals($expectedStatus, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($expectedContent), $content);
    }

    public function editWithValidation()
    {
        return [
            [
                [
                    'id' => 99,
                    'name' => 'username99',
                    'email' => 'email@example.com',
                ],
                404,
                ['error' => 'User with id: 99 - not found!']
            ],
            [
                [
                    'id' => 1,
                    'name' => 'Username1',
                    'email' => 'new.username1@email.com',
                ],
                200,
                [
                    'id' => 1,
                    'name' => 'Username1',
                    'email' => 'new.username1@email.com',
                ]
            ],
        ];
    }
}