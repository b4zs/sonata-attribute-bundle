<?php

namespace Core\AttributeBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

abstract class KernelAwareFormTypeTestCase extends KernelTestCase
{
    /** @var ContainerInterface */
    protected $container;

    /** @var FormFactoryInterface */
    protected $factory;

    /** @var FormBuilder */
    protected $builder;

    /** @var EventDispatcher */
    protected $dispatcher;

    protected function setUp()
    {
        self::bootKernel();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);

        $this->container = static::$kernel->getContainer();
    }

    protected static function createKernel(array $options = array())
    {
        return new \TestKernel('test', 'debug', str_replace('\\','-', get_called_class()).'-'.getmypid());
    }


    public static function assertDateTimeEquals(\DateTime $expected, \DateTime $actual)
    {
        self::assertEquals($expected->format('c'), $actual->format('c'));
    }

    protected function getExtensions()
    {
        return array();
    }
}