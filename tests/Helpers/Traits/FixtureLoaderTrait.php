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
     */
    public function loadFixtures()
    {
        $loader = new Loader();
        $fixtureClasses = $this->fixtures();

        foreach ($fixtureClasses as $fixtureClass) {
            $loader->addFixture(new $fixtureClass());
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}