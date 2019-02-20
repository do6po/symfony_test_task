<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 12:58
 */

namespace Tests\AppBundle\Controller;



use Tests\Helpers\Traits\FixtureLoaderTrait;
use Tests\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixtureLoaderTrait;

    public function testList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/users');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        dump(json_decode($response));die;

//        $this->assertEquals(7, );
    }

    public function testShow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/users/1');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}