<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

interface ProviderInterface{

    /**
     * !!validation needed!!
     * it supposably provides the options that are editable on via the forms when
     * configuring the type (administration interface)
     *
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

    /**
     * Forces options to be set during formType configuration
     *
     * @param array
     * @return array
     */
    public function appendConstraints($options);

    /**
     * @return string
     */
    public function getShowTemplate();

}
