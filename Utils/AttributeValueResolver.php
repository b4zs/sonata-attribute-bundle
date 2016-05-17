<?php


namespace Core\AttributeBundle\Utils;


use Core\AttributeBundle\Entity\CollectionAttribute;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class AttributeValueResolver
{

    /** @var  PropertyAccessor */
    private $propertyAccessor;

    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param object $object
     * @param string $property
     * @param string $attributePath
     * @return mixed|null
     */
    public function getValue($object, $attributePath, $property = 'attributes')
    {
        $attributes = $this->propertyAccessor->getValue($object, $property);

        $pathParts = explode('.', $attributePath);
        if($attributes instanceof CollectionAttribute && current($pathParts) === $attributes->getType()->getName()){
            unset($pathParts[key($pathParts)]);
        }
        $attributePath = implode('.', $pathParts);

        return $this->propertyAccessor->getValue($object, sprintf('%s.%s', $property, $attributePath)) ? : null;
    }


}