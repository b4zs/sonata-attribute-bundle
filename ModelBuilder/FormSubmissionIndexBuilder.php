<?php

namespace Core\AttributeBundle\ModelBuilder;


use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\FormSubmission;
use Core\ToolsBundle\ModelBuilder\ChainableModelBuilder;

class FormSubmissionIndexBuilder extends ChainableModelBuilder
{

    public function build($source, array $options = array(), &$result = array())
    {
        if ($source instanceof FormSubmission AND $this->shouldBuild($options) && null === $source->getDeletedAt()) {
            $collection = $source->getCollection();
            $result = $result + $this->collectionAttributeToArray($collection);
        }

        return parent::build($source, $options, $result);
    }

    private function collectionAttributeToArray(CollectionAttribute $collection, &$out = array()){

        foreach($collection->getValue() as $collectionValue){
            if($collectionValue instanceof CollectionAttribute){
                $this->collectionAttributeToArray($collectionValue, $out);
            }else{
                /** @var Attribute $collectionValue */
                if(is_scalar($collectionValue->getValue()) || is_callable(array($collectionValue->getValue(), '__toString'))){
                    $value = (string)$collectionValue->getValue();
                }else{
                    $value = json_encode($collectionValue->getValue());
                }

                $out[$collectionValue->getType()->getId()] = $value;
            }
        }

        return $out;
    }

}
