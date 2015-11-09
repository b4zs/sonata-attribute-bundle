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
            array(
                'data' => array(
                    'preset' => 'choice',
                    'provider' => 'core_attribute.form_type_options_provider.choice',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'country',
                    'provider' => 'core_attribute.form_type_options_provider.country',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'language',
                    'provider' => 'core_attribute.form_type_options_provider.language',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'locale',
                    'provider' => 'core_attribute.form_type_options_provider.locale',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'timezone',
                    'provider' => 'core_attribute.form_type_options_provider.timezone',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'currency',
                    'provider' => 'core_attribute.form_type_options_provider.currency',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'date',
                    'provider' => 'core_attribute.form_type_options_provider.date',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'time',
                    'provider' => 'core_attribute.form_type_options_provider.time',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'datetime',
                    'provider' => 'core_attribute.form_type_options_provider.datetime',
                ),
            ),
            array(
                'data' => array(
                    'preset' => 'checkbox',
                    'provider' => 'core_attribute.form_type_options_provider.checkbox',
                ),
            ),
        );
    }

}