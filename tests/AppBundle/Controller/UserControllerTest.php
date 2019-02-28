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
use Tests\AppBundle\Fixtures\UsersGroupsFixture;
use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\WebTestCase;

class UserControllerTest extends WebTestCase
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

    public function testList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $response = $client->getResponse();
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
        $client = static::createClient();

        $data = [
            'name' => 'newUsername',
            'email' => 'new.email@example.com'
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $data);

        $client->request(
            'POST',
            '/api/users',
            [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
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
        $client = static::createClient();

        $data = [
            'name' => 'newUsername',
            'email' => 'new.email@example.com'
        ];

        $this->assertDatabaseMissing(User::TABLE_NAME, $data);

        $client->request('POST', '/api/users', $data);

        $response = $client->getResponse();
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
        $client = static::createClient();

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

        $client->request('PUT', sprintf('/api/users/%s', $id), $newData);

        $response = $client->getResponse();
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

        $client = static::createClient();
        $client->request('DELETE', sprintf('/api/users/%s', $userId));

        $this->assertDatabaseMissing(User::TABLE_NAME, [
            'id' => $userId,
        ]);
    }

//    /**
//     * @throws \Doctrine\DBAL\DBALException
//     * @throws \Doctrine\DBAL\Query\QueryException
//     */
//    public function testDeleteNotFound()
//    {
//        $userId = 999;
//
//        $this->assertDatabaseMissing(User::TABLE_NAME, [
//            'id' => $userId,
//        ]);
//
//        $client = static::createClient();
//        $client->request('DELETE', sprintf('/api/users/%s', $userId));
//
//        $response = $client->getResponse();
//
//        $this->assertJson($response->getContent());
//    }

    /**
     * @param $requestData
     * @param $expectedStatus
     * @param $expectedContent
     *
     * @dataProvider createWithValidation
     */
    public function testCreateWithValidation($requestData, $expectedStatus, $expectedContent)
    {
        $client = static::createClient();

        $client->request('POST', sprintf('/api/users'), $requestData);

        $response = $client->getResponse();
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
}