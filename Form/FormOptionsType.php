<?php

namespace Core\AttributeBundle\Form;

use Core\AttributeBundle\Utils\FormOptionFormTypResolverInterface;
use Core\AttributeBundle\Utils\FormOptionFormTypResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class FormOptionsType extends AbstractType{

    /** @var array */
    private $options;

    /** @var string */
    private $formType;

    /** @var FormOptionFormTypResolverInterface */
    private $formOptionResolver;

    /**
     * FormOptionsType constructor.
     * @param array $options
     * @param string $formType
     */
    public function __construct(array $options, $formType)
    {
        $this->options = $options;
        $this->formType = $formType;
        $this->formOptionResolver = new FormOptionFormTypResolver();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($this->options as $key => $option) {
            if (!$this->isHiddenOption($key)) {
                list($child, $type, $childOptions) = $this->formOptionResolver->resolve($key, $this->formType);
                $builder->add($child, $type, $childOptions);

                if ($key == "attr") {
                    foreach ($option as $attrKey => $attrOption) {
                        list($child, $type, $childOptions) = $this->formOptionResolver->resolveAttr($attrKey, $this->formType);
                        $builder->get('attr')->add($child, $type, array('required' => true));
                    }
                }

            }
        }
    }

    public function getName()
    {
        return 'form_options';
    }

    private function isHiddenOption($option){
        $hiddenOptions = array(
            'attribute_class',
            'value_class',
            'label',
            'data_class',
        );

        return in_array($option, $hiddenOptions);
    }

}