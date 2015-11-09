<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Media extends AbstractProvider{

    /** @var string */
    private $mediaClass;

    /**
     * Media constructor.
     * @param string $mediaClass
     */
    public function __construct($mediaClass)
    {
        $this->mediaClass = $mediaClass;
    }

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\MediaAttribute',
            'data_class' => $this->mediaClass,
            'context' => 'default',
            'provider' => 'sonata.media.provider.file',
        ));
    }

}