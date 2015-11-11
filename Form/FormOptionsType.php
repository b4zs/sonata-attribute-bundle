<?php

namespace Core\AttributeBundle\Form;

use Core\AttributeBundle\Event\FormOptionResolveEvent;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;
use Core\AttributeBundle\Utils\FormOptionFormTypeResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormOptionsType extends AbstractType{

    /** @var ProviderChain */
    private $optionsProviderChain;

    /** @var EventDispatcher */
    private $dispatcher;

    /**
     * @param ProviderChain $optionsProviderChain
     * @param EventDispatcher $dispatcher
     */
    public function __construct(ProviderChain $optionsProviderChain, $dispatcher)
    {
        $this->optionsProviderChain = $optionsProviderChain;
        $this->dispatcher = $dispatcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formOptionResolver = new FormOptionFormTypeResolver();

        $formType = $options['form_type'];
        $hiddenFields = $options['hidden_option_fields'];
        $provider = $this->optionsProviderChain->getProvider($formType);

        foreach($provider->getOptions() as $key => $option) {
            if (!in_array($key, $hiddenFields)) {

                $resolveEvent = new FormOptionResolveEvent($key, $formType);
                $this->dispatcher->dispatch(FormOptionResolveEvent::OPTION_RESOLVE, $resolveEvent);

                if(!is_array($resolveEvent->getResult())){
                    throw new \RuntimeException(sprintf('None of the registered event listeners were able to resolve option key: "%s" for type: "%s"', $key, $formType));
                }

                list($child, $type, $childOptions) = $resolveEvent->getResult();
                $builder->add($child, $type, $childOptions);

                if ($key == "attr") {
                    foreach ($option as $attrKey => $attrOption) {

                        $resolveEvent = new FormOptionResolveEvent($attrKey, $formType);
                        $this->dispatcher->dispatch(FormOptionResolveEvent::OPTION_RESOLVE_ATTR, $resolveEvent);

                        if(!is_array($resolveEvent->getResult())){
                            throw new \RuntimeException(sprintf('None of the registered event listeners were able to resolve option attr key: "%s" for type: "%s"', $attrKey, $formType));
                        }

                        list($child, $type, $childOptions) = $resolveEvent->getResult();
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
                'provider',
                'context',
            )
        ));
    }

    public function getName()
    {
        return 'form_options';
    }

}