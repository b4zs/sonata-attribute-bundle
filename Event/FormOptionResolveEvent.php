<?php

namespace Core\AttributeBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class FormOptionResolveEvent extends Event
{

    const
        OPTION_RESOLVE = 'core_attribute.option_resolve',
        OPTION_RESOLVE_ATTR = 'core_attribute.option_resolve_attr';

    protected $option;

    protected $formType;

    protected $result;

    /**
     * FormOptionResolveEvent constructor.
     * @param $option
     * @param $formType
     */
    public function __construct($option, $formType)
    {
        $this->option = $option;
        $this->formType = $formType;
    }

    /**
     * @return mixed
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @return mixed
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

}