<?php

namespace Core\AttributeBundle\Form;

use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;
use Core\AttributeBundle\Utils\FormOptionFormTypResolverInterface;
use Core\AttributeBundle\Utils\FormOptionFormTypResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormOptionsType extends AbstractType{

    /** @var ProviderChain */
    private $optionsProviderChain;

    /**
     * @param ProviderChain $optionsProviderChain
     */
    public function __construct(ProviderChain $optionsProviderChain)
    {
        $this->optionsProviderChain = $optionsProviderChain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formOptionResolver = new FormOptionFormTypResolver();

        $formType = $options['form_type'];
        $hiddenFields = $options['hidden_option_fields'];
        $provider = $this->optionsProviderChain->getProvider($formType);

        foreach($provider->getOptions() as $key => $option) {
            if (!in_array($key, $hiddenFields)) {
                list($child, $type, $childOptions) = $formOptionResolver->resolve($key, $formType);
                $builder->add($child, $type, $childOptions);

                if ($key == "attr") {
                    foreach ($option as $attrKey => $attrOption) {
                        list($child, $type, $childOptions) = $formOptionResolver->resolveAttr($attrKey, $formType);
                        $builder->get('attr')->add($child, $type, array('required' => true));
                    }
                }

            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'form_type' => null,
            'hidden_option_fields' => array(
                'attribute_class',
                'label',
                'data_class',
            )
        ));
    }

    public function getName()
    {
        return 'form_options';
    }

}