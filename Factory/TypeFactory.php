<?php

namespace Core\AttributeBundle\Factory;

use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderInterface;

class TypeFactory
{

    /** @var ProviderChain */
    private $optionsProviderChain;

    /**
     * @param ProviderChain $optionsProviderChain
     */
    public function __construct(ProviderChain $optionsProviderChain)
    {
        $this->optionsProviderChain = $optionsProviderChain;
    }

    public function create($preset){

        $optionProvider = $this->optionsProviderChain->getProvider($preset);
        $this->validateOptionProviderOptions($optionProvider);

        $type = new Type();

        $type->setAttributeClass($optionProvider->getOption('attribute_class'));
        $type->setDataClass($optionProvider->getOption('data_class'));
        $type->setFormType($preset);
        $type->setFormOptions($optionProvider->getOptions());

        if($optionProvider->getOption('label')) $type->setLabel($optionProvider->getOption('label'));

        return $type;
    }

    /**
     * @param $optionProvider
     */
    private function validateOptionProviderOptions(ProviderInterface $optionProvider)
    {
        if (!$optionProvider->hasOption('attribute_class') || !$optionProvider->hasOption('data_class')) {
            throw new \RuntimeException(sprintf('Both "%s" and "%s" mut be defined in the option provider', 'attribute_class', 'data_class'));
        }

        if (!is_subclass_of($optionProvider->getOption('attribute_class'), 'Core\AttributeBundle\Entity\Attribute')) {
            throw new \RuntimeException(sprintf('"%s" must be a subclass of "Core\AttributeBundle\Entity\Attribute", instance of "%s" given', 'attribute_class', $optionProvider->getOption('attribute_class')));
        }

        if ($optionProvider->getOption('data_class') && !class_exists($optionProvider->getOption('data_class'))) {
            throw new \RuntimeException(sprintf('Non existent class ("%s") has been defined as "%s"', $optionProvider->getOption('data_class'), 'data_class'));
        }
    }

}