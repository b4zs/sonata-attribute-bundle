<?php

namespace Core\AttributeBundle\Tests\Form;

use Core\AttributeBundle\Factory\TypeFactory;
use Core\AttributeBundle\Form\DynamicFormType;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;
use Core\AttributeBundle\Tests\KernelAwareFormTypeTestCase;

class DynamicFormTypeTest extends KernelAwareFormTypeTestCase
{
    /** @var TypeFactory */
    private $typeFactory;

    /** @var ProviderChain */
    private $providerChain;

    protected function setUp()
    {
        parent::setUp();

        $this->typeFactory = $this->container->get('core_attribute.factory.type');
        $this->providerChain = $this->container->get('core_attribute.form_type_options_provider.provider_chain');
    }

    private function buildType($types){
        $rootType = $this->typeFactory->create('form');

        foreach($types as $name => $typeAttributes){
            $type = $this->typeFactory->create($typeAttributes['type']);
            $type->setName($name);
            $type->setFormOptions(array_replace_recursive($type->getFormOptions(), $typeAttributes['options']));
            $rootType->addChildren($type);
        }

        return $rootType;
    }

    private function getSubmittedFormData($formData){
        return array_map(function($value){
           return $value['value'];
        }, $formData);
    }

    /**
     * @dataProvider getTypesData
     */
    public function testSubmitValidData($data)
    {
        $rootType = $this->buildType($data['types']);

        $dynamicFormType = new DynamicFormType($this->providerChain, $rootType);
        $dynamicForm = $this->factory->create($dynamicFormType);

        $dynamicForm->submit($this->getSubmittedFormData($data['formData']));

        $this->assertTrue($dynamicForm->isSynchronized());

        /** @var \Core\AttributeBundle\Entity\CollectionAttribute $formResult */
        $formResult = $dynamicForm->getData();
        $this->validateFormData($data, $formResult);

        $view = $dynamicForm->createView();
        $children = $view->children;

        foreach (array_keys($data['types']) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @param $data
     * @param $formResult
     */
    private function validateFormData($data, $formResult)
    {
        $this->assertInstanceOf('Core\AttributeBundle\Entity\CollectionAttribute', $formResult);

        //this can also be failed if invalid data was submitted
        //for example, if you submit a value of array(1,2) to a choice which has no options like "multiple"
        $this->assertEquals(count($data['formData']), $formResult->getValue()->count());

        foreach ($formResult->getValue() as $result) {
            $expectedData = $data['formData'][$result->getType()->getName()];
            $this->assertInstanceOf($expectedData['attributeClass'], $result);
//            $this->assertEquals($expectedData['value'], $result->getValue());
        }
    }

    public function getTypesData(){

        return array(
            array(
                'data' => array(
                    'types' => array(
                        'name' => array(
                            'type' => 'text',
                            'options' => array(),
                        ),
                        'email' => array(
                            'type' => 'email',
                            'options' => array(),
                        ),
                        'message' => array(
                            'type' => 'textarea',
                            'options' => array(),
                        ),
                        'gender' => array(
                            'type' => 'choice',
                            'options' => array(
                                'choices' => array(1 => 'test1', 2 => 'test2'),
                                'multiple' => true,
                            ),
                        ),
                        'country' => array(
                            'type' => 'country',
                            'options' => array(),
                        ),
                        'birthday' => array(
                            'type' => 'date',
                            'options' => array(),
                        ),
                    ),
                    'formData' => array(
                        'name' => array(
                            'attributeClass' => 'Core\AttributeBundle\Entity\StringAttribute',
                            'value' => 'Test Name',
                        ),
                        'email' => array(
                            'attributeClass' => 'Core\AttributeBundle\Entity\StringAttribute',
                            'value' => 'tester@testcompany.com',
                        ),
                        'message' => array(
                            'attributeClass' => 'Core\AttributeBundle\Entity\StringAttribute',
                            'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tristique est arcu, ac porttitor augue maximus ac.',
                        ),
                        'gender' => array(
                            'attributeClass' => 'Core\AttributeBundle\Entity\JsonAttribute',
                            'value' => array(1,2),
                        ),
                        'country' => array(
                            'attributeClass' => 'Core\AttributeBundle\Entity\JsonAttribute',
                            'value' => 'HU',
                        ),
                        'birthday' => array(
                            'attributeClass' => 'Core\AttributeBundle\Entity\DateTimeAttribute',
                            'value' => array(
                                'date' => array(
                                    'month' => '2',
                                    'day'   => '2',
                                    'year'  => '2010'
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

    }

}