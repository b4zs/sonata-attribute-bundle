<?php

namespace Core\AttributeBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;

class DynamicEntityListener
{
    /** @var string */
    private $mediaClass;

    /**
     * DynamicEntityListener constructor.
     * @param string $mediaClass
     */
    public function __construct($mediaClass)
    {
        $this->mediaClass = $mediaClass;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if(class_exists($this->mediaClass) && $entity instanceof $this->mediaClass) {
            $mediaAttributes = $entityManager->getRepository('CoreAttributeBundle:MediaAttribute')->findBy(array(
                'mediaValue' => $entity,
            ));

            $this->setNull($mediaAttributes, 'setValue');
        }
    }

    private function setNull($objects, $method){

        foreach($objects as $object){
            call_user_func_array(array($object, $method), array(null));
        }

    }

}