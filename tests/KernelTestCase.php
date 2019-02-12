<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 19:00
 */

namespace Tests;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
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
     * @param string $repositoryClass
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function assertDatabaseHas(string $repositoryClass, array $data)
    {
        $tableName = $this->getTableNameByRepositoryClass($repositoryClass);
        $repository = $this->getRepositoryByClass($repositoryClass);
        $entity = $repository->findOneBy($data);

        $this->assertNotNull($entity,
            sprintf("a row in the table [%s] matches with data %s.\n", $tableName, json_encode($data, JSON_PRETTY_PRINT))
        );
    }

    /**
     * @param string $repositoryClass
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function assertDatabaseMissing(string $repositoryClass, array $data)
    {
        $tableName = $this->getTableNameByRepositoryClass($repositoryClass);
        $repository = $this->getRepositoryByClass($repositoryClass);
        $entity = $repository->findOneBy($data);

        $this->assertNull($entity,
            sprintf("not one row in the table: %s not matches with data %s.\n", $tableName, json_encode($data, JSON_PRETTY_PRINT))
        );
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->container = null;
    }

    /**
     * @param string $repositoryClass
     * @return string
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    protected function getTableNameByRepositoryClass(string $repositoryClass)
    {
        return $this->entityManager->getClassMetadata($repositoryClass)->getTableName();
    }

    /**
     * @param string $repositoryClass
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepositoryByClass(string $repositoryClass)
    {
        return $this->entityManager->getRepository($repositoryClass);
    }
}