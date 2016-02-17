<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;


interface ChoiceProviderInterface
{

    /**
     * @return array
     */
    public function getPreferredOptions();

}
