<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 20.02.19
 * Time: 12:55
 */

namespace Tests\Helpers\Traits;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;


/**
 * Trait DatabaseFinderTrait
 *
 * @method assertTrue($data, $message = '')
 *
 * @property EntityManager $entityManager
 *
 * @package Tests\Helpers\Traits
 */
trait DatabaseFinderTrait
{

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