<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 12:58
 */

namespace Tests\AppBundle\Controller;


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

    public function testShow()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/1');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}