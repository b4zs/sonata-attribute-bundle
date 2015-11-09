<?php

namespace Core\AttributeBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreAttributeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($container->hasParameter('sonata.media.admin.media.entity')) {
            $mediaClass = $container->getParameter('sonata.media.admin.media.entity');

            $this->registerDoctrineMapping(array(
                'media_class' => $mediaClass,
            ));

        }
    }

    private function registerDoctrineMapping($config)
    {
        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation('Core\AttributeBundle\Entity\MediaAttribute', 'mapManyToOne', array(
            'fieldName'     => 'mediaValue',
            'targetEntity'  => $config['media_class'],
            'cascade'       => array('persist', 'refresh', 'merge'),
            'joinColumns'    => array(
                array(
                    'name'     => 'media_value',
                    'onDelete' => 'SET NULL',
                ),
            ),
        ));
    }
}
