<?php

namespace Core\AttributeBundle\Utils;

interface FormOptionFormTypResolverInterface{

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
