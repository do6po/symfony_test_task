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
     * @param string $entityClassName
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function assertDatabaseHas(string $entityClassName, array $data)
    {
        $tableName = $this->getTableNameByEntityClass($entityClassName);
        $repository = $this->getRepositoryByEntityClass($entityClassName);
        $entity = $repository->findOneBy($data);

        $this->assertNotNull($entity,
            sprintf("In the table [%s] not found data %s.\n", $tableName, json_encode($data, JSON_PRETTY_PRINT))
        );
    }

    /**
     * @param string $entityClassName
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function assertDatabaseMissing(string $entityClassName, array $data)
    {
        $tableName = $this->getTableNameByEntityClass($entityClassName);
        $repository = $this->getRepositoryByEntityClass($entityClassName);
        $entity = $repository->findOneBy($data);

        $this->assertNull($entity,
            sprintf("In the table: [%s] found data %s.\n", $tableName, json_encode($data, JSON_PRETTY_PRINT))
        );
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

    /**
     * @param string $entityClassName
     * @return string
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    protected function getTableNameByEntityClass(string $entityClassName)
    {
        return $this->entityManager->getClassMetadata($entityClassName)->getTableName();
    }

    /**
     * @param string $entityClassName
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepositoryByEntityClass(string $entityClassName)
    {
        return $this->entityManager->getRepository($entityClassName);
    }
}