<?php

namespace Core\AttributeBundle\Utils;

interface FormOptionFormTypeResolverInterface{

    /**
     * @param string $option
     * @param string $formType
     */
    public function resolve($option, $formType);

    /**
     * @param string $option
     * @param string $formType
     */
    public function resolveAttr($option, $formType);

}
