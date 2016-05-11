<?php

namespace Core\AttributeBundle\Twig\Extension;

use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;

class AttributeExtension extends \Twig_Extension
{
    /** @var ProviderChain */
    private $optionsProviderChain;

    public function __construct(ProviderChain $optionsProviderChain)
    {
        $this->optionsProviderChain = $optionsProviderChain;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('flatten_collection_attribute', array($this, 'flattenCollectionAttribute')),
        );
    }

    public function flattenCollectionAttribute($collection, $linkManagedObjects = true)
    {

        if(!$collection instanceof CollectionAttribute){
            throw new \InvalidArgumentException(sprintf('Input must be an instance of "%s"', 'Core\AttributeBundle\Entity\CollectionAttribute'));
        }

        $collectionAttributeValues = $this->collectionAttributeToArray($collection);
        return $collectionAttributeValues;
    }

    private function collectionAttributeToArray(CollectionAttribute $collection, &$out = array()){

        /** @var Attribute $collectionValue */
        foreach($collection->getValue() as $collectionValue){
            if($collectionValue instanceof CollectionAttribute){
                $this->collectionAttributeToArray($collectionValue, $out);
            }else{
                if(is_scalar($collectionValue->getValue()) || is_callable(array($collectionValue->getValue(), '__toString'))){
                    $path = $this->getPath($collectionValue);
                    $provider = $this->optionsProviderChain->getProvider($collectionValue->getType()->getFormType());

                    $out[$path] = array(
                        'label' => $collectionValue->getType()->getLabel()?:$collectionValue->getType()->getName(),
                        'value' => $collectionValue->getValue(),
                        'template' => $provider->getShowTemplate(),
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

    /**
     * @param Attribute $collectionValue
     * @return array|string
     */
    private function getPath($collectionValue)
    {
        $path = array();
        /** @var Type $type */
        foreach ($collectionValue->getType()->buildPath() as $type) {
            $path[] = $type->getName();
        }
        $path = implode('.', $path);
        return $path;
    }
}
