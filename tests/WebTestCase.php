<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 20.02.19
 * Time: 12:53
 */

namespace Tests;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Helpers\Traits\DatabaseFinderTrait;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    use DatabaseFinderTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function setUp()
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $this->container = $kernel->getContainer();

        $this->entityManager = $this->container->get('doctrine')->getManager();
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->container = null;
    }
}