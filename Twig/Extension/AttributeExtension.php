<?php

namespace Core\AttributeBundle\Twig\Extension;

use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\CollectionAttribute;

class AttributeExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('flatten_collection_attribute', array($this, 'flattenCollectionAttribute')),
        );
    }

    public function flattenCollectionAttribute($collection)
    {

        if(!$collection instanceof CollectionAttribute){
            throw new \InvalidArgumentException(sprintf('Input must be an instance of "%s"', 'Core\AttributeBundle\Entity\Colelction'));
        }

        return $this->collectionAttributeToArray($collection);
    }

    private function collectionAttributeToArray(CollectionAttribute $collection, &$out = array()){

        /** @var Attribute $collectionValue */
        foreach($collection->getValue() as $collectionValue){
            if($collectionValue instanceof CollectionAttribute){
                $this->collectionAttributeToArray($collectionValue, $out);
            }else{
                if(is_scalar($collectionValue->getValue()) || is_callable(array($collectionValue->getValue(), '__toString'))){
                    $out[] = array(
                        'label' => $collectionValue->getType()->getLabel()?:$collectionValue->getType()->getName(),
                        'value' => (string)$collectionValue->getValue()
                    );
                }
            }
        }

        return $out;
    }

    public function getName()
    {
        return 'attribute';
    }
}
