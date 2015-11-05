<?php
namespace Core\AttributeBundle\Tests;

use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\AttributeBasedType;
use Core\AttributeBundle\Form\Type\DynamicTextType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\Collection;

class DynamicTextTypeTest extends KernelTestCase{

    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $formOptions;

    /** @var FormFactoryInterface */
    private $formFactory;

    public function setUp() {
        self::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->formFactory = $this->container->get('form.factory');


        $this->formOptions = array(
            'label' => 'Test label',
            'trim' => false,
            'attr' => array(
                'maxlength' => 100,
                'readonly' => true,
                'placeholder' => 'Test placeholder',
                'class' => 'col-xs-12',
                'style' => 'background-color: red;',
            ),
        );

    }

    public function testBuildForm(){
//        $type = $this->container->get('core_attribute.form.type.dynamic_text_type');
        $type = new DynamicTextType('Core\AttributeBundle\Entity\StringAttribute');

        $form = $this->formFactory->create($type, null, $this->formOptions);
        $formOptions = $form->getConfig()->getOptions();

        $this->assertInstanceOf('\Symfony\Component\Form\FormInterface', $form);

        foreach($this->formOptions as $defaultOptionKey => $defaultOption){
            $this->assertArrayHasKey($defaultOptionKey, $formOptions);
            $this->assertEquals($defaultOption, $formOptions[$defaultOptionKey]);
        }
    }

    public function testRenderForm(){

        $formData = new \Core\AttributeBundle\Entity\StringAttribute();
        $formData->setValue('test data');

        $type = new DynamicTextType('Core\AttributeBundle\Entity\StringAttribute');

        $form = $this->formFactory->create($type, null, $this->formOptions);

        $view = $form->createView();

        $html = $this->container->get('twig')->render('@CoreAttribute/Test/form.html.twig', array('form' => $view));

        $crawler = new Crawler($html);
        $input = $crawler->filter(sprintf("[name=%s]", $view->vars['name']));

        $this->assertEquals(1, $input->count());

        foreach($this->formOptions['attr'] as $attr => $value){
            $value = $attr == "readonly"?"readonly":$value;
            $this->assertEquals($value, $input->attr($attr));
        }

    }

    public function testSubmitForm(){
//        $formData = new \Core\AttributeBundle\Entity\StringAttribute();
//        $formData->setValue('test data');

        $type = new DynamicTextType('Core\AttributeBundle\Entity\StringAttribute');

        $type = Type::create('', '', $type->getOptions()['attribute_class'], $type->getOptions()['value_class'], $type->getName(), $this->formOptions);

        $rootFormType = new AttributeBasedType($type);

        $form = $this->formFactory->create($rootFormType);

        $form->submit('test data');

        //only set to false if a data transformer throws an exception
        $this->assertTrue($form->isSynchronized());
        $this->assertInstanceOf('Core\AttributeBundle\Entity\StringAttribute', $form->getData());
        $this->assertEquals('test data', $form->getData()->getValue());

    }


}