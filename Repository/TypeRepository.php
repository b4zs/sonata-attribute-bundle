<?php

namespace Core\AttributeBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TypeRepository extends EntityRepository
{
    public function fetchDynamicRootTypes()
    {
        return $this->createDynamicRootTypesQuery()
            ->getQuery()
            ->getResult();
    }

    public function createDynamicRootTypesQuery()
    {
        return $this
            ->createQueryBuilder('t')
            ->andWhere('t.parent IS NULL')
        ;
    }

}