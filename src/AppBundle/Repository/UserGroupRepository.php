<?php

namespace AppBundle\Repository;


use AppBundle\Entity\UserGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * UserGroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroup::class);
    }

    public function save(UserGroup $group): void
    {
        $this->_em->persist($group);
        $this->_em->flush();
    }

    public function remove(UserGroup $group): void
    {
        $this->_em->remove($group);
        $this->_em->flush();
    }
}
