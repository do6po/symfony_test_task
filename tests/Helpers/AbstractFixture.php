<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 14.02.19
 * Time: 19:29
 */

namespace Tests\Helpers;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

abstract class AbstractFixture extends Fixture
{
    protected $dataPath =  __DIR__ . '';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function load(ObjectManager $manager);

    protected function getFixtureData()
    {
        return require $this->dataPath;
    }
}