<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 14.02.19
 * Time: 19:23
 */

namespace Tests\AppBundle\Fixtures;


use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\Helpers\AbstractFixture;

class UsersGroupsIntermediate extends AbstractFixture implements DependentFixtureInterface
{
    protected $dataPath = __DIR__ . '/Data/users_groups.php';

    public function load(ObjectManager $manager)
    {
        $object = $manager->getRepository(User::class);
        dump($object);die;
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            UserGroupFixture::class,
        ];
    }
}