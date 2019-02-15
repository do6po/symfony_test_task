<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 14.02.19
 * Time: 19:23
 */

namespace Tests\AppBundle\Fixtures;


use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Tests\Helpers\AbstractFixture;

class UsersGroupsFixture extends AbstractFixture implements DependentFixtureInterface
{
    protected $dataPath = __DIR__ . '/Data/users_groups.php';

    /**
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Query\QueryException
     */
    public function load(ObjectManager $manager)
    {
        $queryBuilder = new QueryBuilder($this->entityManager->getConnection());
        $queryBuilder->insert('users_groups');

        foreach ($this->getFixtureData() as $data) {
            $queryBuilder->values($data);
        }

        $queryBuilder->execute();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            UserGroupFixture::class,
        ];
    }
}