<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

interface ProviderInterface{

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $option
     * @return mixed
     */
    public function getOption($option);

    /**
     * @param string $option
     * @return boolean
     */
    public function hasOption($option);

}