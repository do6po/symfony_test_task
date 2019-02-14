<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 12.02.19
 * Time: 17:59
 */

namespace Tests\AppBundle\Fixtures;


use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserGroupFixture extends Fixture
{
    protected $dataPath = 'Data/user_groups.php';

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $fixtureData = $this->getFixtureData();

        foreach ($fixtureData as $row) {
            $group = new UserGroup();
            $group->setName($row['name']);

            $manager->persist($group);
        }

        $manager->flush();
    }

    protected function getFixtureData()
    {
        return require __DIR__ . DIRECTORY_SEPARATOR . $this->dataPath;
    }
}