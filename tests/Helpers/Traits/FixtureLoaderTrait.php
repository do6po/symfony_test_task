<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 12.02.19
 * Time: 18:10
 */

namespace Tests\Helpers\Traits;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Tests\Helpers\AbstractFixture;

/**
 * @property EntityManager entityManager
 */
trait FixtureLoaderTrait
{
    public function fixtures(): array
    {
        return [];
    }

    /**
     * @throws \Doctrine\Common\DataFixtures\Exception\CircularReferenceException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function loadFixtures()
    {
        $loader = new Loader();
        $fixtureClasses = $this->fixtures();

        foreach ($fixtureClasses as $fixtureClass) {
            /** @var AbstractFixture $fixture */
            $fixture = new $fixtureClass();
            $fixture->setEntityManager($this->entityManager);
            $loader->addFixture($fixture);
        }

        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);

        $this->disableForeignKeyChecks();

        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function disableForeignKeyChecks()
    {
        $this->entityManager
            ->getConnection()
            ->exec('SET FOREIGN_KEY_CHECKS=0');
    }
}