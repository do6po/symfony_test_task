<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 11.02.19
 * Time: 19:00
 */

namespace Tests;


use Doctrine\DBAL\Query\QueryBuilder;
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

    public function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->container = null;
    }

    /**
     * @param string $tableName
     * @param array $data
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function assertDatabaseHas(string $tableName, array $data)
    {
        $result = $this->findInTableByCondition($tableName, $data);

        $this->assertTrue(count($result) > 0 ,
            sprintf("In the table [%s] not found data %s.\n", $tableName, json_encode($data, JSON_PRETTY_PRINT))
        );
    }

    /**
     * @param string $tableName
     * @param array $data
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function assertDatabaseMissing(string $tableName, array $data)
    {
        $result = $this->findInTableByCondition($tableName, $data);

        $this->assertTrue(count($result) === 0 ,
            sprintf("In the table: [%s] found data %s.\n", $tableName, json_encode($data, JSON_PRETTY_PRINT))
        );
    }

    protected function getQueryBuilder()
    {
        return new QueryBuilder($this->entityManager->getConnection());
    }

    /**
     * @param string $tableName
     * @param array $condition
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function findInTableByCondition(string $tableName, array $condition)
    {
        $query = $this->getQueryBuilder();

        $query->select('*')
            ->from($tableName);

        foreach ($condition as $key => $value) {
            $query->andWhere(sprintf('%s = %s', $key, $query->createPositionalParameter($value)));
        }

        return $query->execute()->fetchAll();
    }
}