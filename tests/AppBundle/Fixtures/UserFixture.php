<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 12.02.19
 * Time: 17:59
 */

namespace Tests\AppBundle\Fixtures;


use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    protected $dataPath = 'Data/users.php';

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $fixtureData = $this->getFixtureData();

        foreach ($fixtureData as $row) {
            $user = new User();
            $user->setName($row['name']);
            $user->setEmail($row['email']);

            $manager->persist($user);
        }

        $manager->flush();
    }

    protected function getFixtureData()
    {
        return require __DIR__ . DIRECTORY_SEPARATOR . $this->dataPath;
    }
}