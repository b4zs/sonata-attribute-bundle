<?php

namespace Core\AttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OptionsProviderCompilerPass implements CompilerPassInterface{

    public function process(ContainerBuilder $container)
    {

        if (!$container->hasDefinition('core_attribute.form_type_options_provider.provider_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'core_attribute.form_type_options_provider.provider_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'dynamic_form.options_provider'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addProvider',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }

    }

}

