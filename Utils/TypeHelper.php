<?php

namespace Core\AttributeBundle\Utils;


use Core\AttributeBundle\Entity\Type;

class TypeHelper
{

    static function flattenType(Type $type, $out = array()){
        foreach($type->getChildren() as $child){
            if($type->getChildren()->count()){
                $out = self::flattenType($child, $out);
            }
        }

        if($type->getChildren()->count() == 0){
            $current = $type;
            $a = array($current->getName());
            while ($current = $current->getParent()) {
                $a[] = $current->getName();
            }
            $out[$type->getLabel().'_'.$type->getId()] = implode('.',array_reverse($a));
        }

        return $out;
    }

}