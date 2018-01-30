<?php

namespace Core\AttributeBundle;

use Core\AttributeBundle\DependencyInjection\Compiler\OptionsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreAttributeBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new OptionsProviderCompilerPass());

    }

}
