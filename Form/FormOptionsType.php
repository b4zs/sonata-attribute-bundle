<?php

namespace Core\AttributeBundle\Form;

use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Event\FormOptionResolveEvent;
use Core\AttributeBundle\FormTypeOptionsProvider\ChoiceProviderInterface;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;
use Core\AttributeBundle\Utils\FormOptionFormTypeResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        //success flash remove on forms with parent form(only root form have success_flash)
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            /** @var Type $data */
            $data = $event->getForm()->getParent()->getData();
            if(null !== $data) {
                if('form' === $data->getFormType() && null !== $data->getParent()) {
                    $event->getForm()->remove('success_flash');
                }
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($provider){

            if(!$event->getForm()->has('preferred_choices')){
                return;
            }

            /** @var Type $data */
            $data = $event->getForm()->getParent()->getData();

            $preferredChoicesField = $event->getForm()->get('preferred_choices');
            $preferredChoices = array();

            if(null !== $data) {
                $dataFormOptions = $data->getFormOptions();

                if(array_key_exists('choices', $dataFormOptions) && is_array($dataFormOptions['choices'])){
                    $preferredChoices = $dataFormOptions['choices'];
                }elseif($provider instanceof ChoiceProviderInterface){
                    if(is_array($provider->getPreferredOptions())){
                        $preferredChoices = $provider->getPreferredOptions();
                    }
                }
            }

            if(count($preferredChoices)){
                $event->getForm()->remove('preferred_choices');
                $options = $preferredChoicesField->getConfig()->getOptions();

                $options['choices'] = $preferredChoices;
                unset($options['choice_list']);
                unset($options['choice_label']);

                $event->getForm()->add('preferred_choices', $preferredChoicesField->getConfig()->getType()->getName(), $options);
            }

        });

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
