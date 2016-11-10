<?php

namespace Core\AttributeBundle\Form\DataTransformer;


use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\Type;
use Symfony\Component\Form\DataTransformerInterface;


class AttributeInitializerTransformer implements DataTransformerInterface
{
    /** @var  Type */
    private $type;

    /**
     * @param Attribute $value
     * @return Attribute|object
     */
    public function transform($value)
    {
        if ($value instanceof Attribute) {
            return $value;
        } else {
            $class = $this->type->getAttributeClass();
            /** @var Attribute $result */
            $result = new $class;
            $result->setType($this->getType());
            return $result;
        }
    }

    /**
     * @param scalar|object
     * @return Attribute
     */
    public function reverseTransform($value)
    {
        if ($value instanceof Attribute) {
            return $value;
        } else {
            $className = $this->type->getAttributeClass();
            /** @var Attribute $attribute */
            $attribute = new $className();
            $attribute->setType($this->type);
            $attribute->setValue($value);

            return $attribute;
        }
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}