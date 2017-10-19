<?php


namespace Core\AttributeBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CollectionAttribute extends Attribute
{
    /** @var  Collection|Attribute[] */
    protected $collectionValue;

    function __construct()
    {
        $this->collectionValue = new ArrayCollection();
    }

    function __toString()
    {
        return get_class($this->getValue());
    }

    public function getValue()
    {
        return $this->collectionValue;
    }

    public function setValue($value)
    {
        $this->assertValue($value);
        $this->collectionValue = $value;
    }

    public function __get($name)
    {
        foreach ($this->getValue() as $childAttribute) {
            if ($childAttribute instanceof Attribute && $childAttribute->getType()) {
                if ($childAttribute->getType()->getName() === $name) {
                    return $childAttribute;
                }
            }
        }

        return null;
    }

    function __set($name, $value)
    {
        if ($value instanceof Attribute) {
            if ($name !== $value->getType()->getName()) {
                throw new \RuntimeException('property name and attribute.type.name differs');
            }

            foreach ($this->getValue() as $key => $child) {
                if ($child->getType()->getName() == $value->getType()->getName()) {
                    $this->getValue()->remove($key);
                    break;
                }
            }

            $value->setParent($this);
            $this->getValue()->add($value);
        } else {
            throw new \InvalidArgumentException('Argument 2. must be an instance of Attribute');
        }
    }

    public function assertValue($value)
    {
        if (!$value instanceof Collection) {
            throw $this->createValueAssertionException('an instance of Doctrine\\Common\\Collections\\Collection', $value);
        }
    }

    public function serialize()
    {

        if (!$this->getValue() instanceof Collection) {
            return null;
        } else {
            $result = array();
            foreach ($this->getValue() as $child) {
                $name = $child->getType() ? $child->getType()->getName() : null;
                $value = $child->serialize();
                if ($name) {
                    $result[$name] = $value;
                } else {
                    $result[] = $value;
                }
            }
            return $result;
        }
    }

    public function __clone()
    {
        parent::__clone();

        $newCollection = new ArrayCollection();

        $this->collectionValue->map(function(Attribute $attribute) use ($newCollection ) {
            $newAttribute = clone $attribute;
            $newAttribute->setParent($this);
            $newCollection->add($newAttribute);
        });
        $this->collectionValue = $newCollection ;
    }
}
