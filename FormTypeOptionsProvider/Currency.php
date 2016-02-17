<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Symfony\Component\Intl\Intl;

class Currency extends Choice{

    public function getOptions(){
        $defaultOptions = parent::getOptions();
        unset($defaultOptions['choices']);
        return $defaultOptions;
    }

    public function getPreferredOptions()
    {
        return Intl::getCurrencyBundle()->getCurrencyNames();
    }

}
