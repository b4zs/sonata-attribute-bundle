<?php

namespace Core\AttributeBundle\Tests\Form;

use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DynamicFormType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DynamicFormTypeTest extends KernelTestCase
{

    /** @var ContainerInterface */
    private $container;

    public function setUp() {
        self::bootKernel();

        $this->container = static::$kernel->getContainer();
    }


    private function buildType(){

        $typeFactory = $this->container->get('core_attribute.factory.type');

        $rootType = $typeFactory->create('form');

        foreach($this->getSubForms() as $ix => $preset){
            $type = $typeFactory->create($preset);
            $type->setName($ix.'_'.$preset);
            $rootType->addChildren($type);
        }

        return $rootType;
    }

    private function getSubForms(){

        return array(
            'text',
            'email',
            'integer',
        );

    }

    public function testBuildForm(){

        $rootType = $this->buildType();

        $formFactory = $this->container->get('form.factory');
        $dynamicFormType = new DynamicFormType($rootType);
        $dynamicForm = $formFactory->createBuilder($dynamicFormType)->getForm();

        \Doctrine\Common\Util\Debug::dump($dynamicForm, 2);

    }

    public function testRenderForm(){

    }

    public function testSubmitForm(){

    }

}