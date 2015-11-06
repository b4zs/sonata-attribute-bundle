<?php
namespace Core\AttributeBundle\Tests;

use Core\AttributeBundle\Factory\TypeFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TypeFactoryTest extends KernelTestCase{

    /** @var ContainerInterface */
    private $container;

    /** @var TypeFactory */
    private $typeFactory;

    public function setUp() {
        self::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->typeFactory = $this->container->get('core_attribute.factory.type');

    }

    /**
     * @dataProvider getPresetsData
     */
    public function testCreate($data){
        $type = $this->typeFactory->create($data['preset']);

        $formOptions = $this->container->get($data['provider'])->getOptions();

        $this->assertInstanceOf('\Core\AttributeBundle\Entity\Type', $type);
        $this->assertEquals($data['preset'], $type->getFormType());
        $this->assertEquals($formOptions, $type->buildFormOptions());
    }

    public function getPresetsData(){
        return array(
            array(
                'data' => array(
                    'preset' => 'email',
                    'provider' => 'core_attribute.form_type_options_provider.email',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'form',
                    'provider' => 'core_attribute.form_type_options_provider.form',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'integer',
                    'provider' => 'core_attribute.form_type_options_provider.integer',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'money',
                    'provider' => 'core_attribute.form_type_options_provider.money',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'number',
                    'provider' => 'core_attribute.form_type_options_provider.number',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'text',
                    'provider' => 'core_attribute.form_type_options_provider.text',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'textarea',
                    'provider' => 'core_attribute.form_type_options_provider.textarea',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'url',
                    'provider' => 'core_attribute.form_type_options_provider.url',
                ),
            ),
        );
    }

}