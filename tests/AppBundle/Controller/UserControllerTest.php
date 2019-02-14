<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 12:58
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/users');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
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