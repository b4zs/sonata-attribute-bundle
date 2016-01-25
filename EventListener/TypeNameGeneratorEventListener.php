<?php

namespace Core\AttributeBundle\EventListener;

use Core\AttributeBundle\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TypeNameGeneratorEventListener
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if($entity instanceof Type){
            $this->generateName($entityManager, $entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if($entity instanceof Type){
            $this->generateName($entityManager, $entity);
        }
    }


    protected function generateName(EntityManagerInterface $entityManager, Type $entity)
    {
        $classMetadata = $entityManager->getClassMetadata(get_class($entity));
        $tableName = $classMetadata->getTableName();

        $stmt = $entityManager->getConnection()->prepare(sprintf('SELECT id FROM %s WHERE name = :name', $tableName));

        $name = $entity->getName();

        if($entity->getId()){
            //this is an update
            $stmt->execute(array('name' => $name));
            $result = $stmt->fetch();

            if($result && $result['id'] == $entity->getId()){
                return false;
            }
        }

        while (true) {
            $uniqueName = sprintf('%s_%d', $name, mt_rand(100000, 999999));

            $stmt->execute(array('name' => $uniqueName));

            if (0 === $stmt->rowCount()) {
                $entity->setName($uniqueName);
                return false;
            }
        }

        throw new \Exception('TypeNameGenerator could not generate unique name');
    }

}