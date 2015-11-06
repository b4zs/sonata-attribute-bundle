<?php


namespace Core\AttributeBundle\Tests;


use Core\AttributeBundle\Entity\BooleanAttribute;
use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\AttributeBasedType;
use Core\AttributeBundle\Form\DataTransformer\AttributeToValueTransformer;
use Core\ToolsBundle\Tests\ContainerAwareTest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Collection;

class TypeBasedFormBuildTest extends KernelTestCase
{
	public function setUp() {
		parent::setUp();
		static::bootKernel();
	}

	/**
	 * @return array
	 */
	public static function buildDemoType()
	{
		$namespace = 'Core\\AttributeBundle\\Entity\\';

		$rootType = new Type();
		$rootType->setName('form');
		$rootType->setFormType('form');
		$rootType->setAttributeClass($namespace . 'CollectionAttribute');
		$rootType->setDataClass($namespace.'CollectionAttribute');

		$usernameType = new Type();
		$usernameType->setName('username');
		$usernameType->setLabel('username');
		$usernameType->setAttributeClass($namespace . 'StringAttribute');
		$usernameType->setFormType('text');
		$rootType->addChildren($usernameType);

		$idType = new Type();
		$idType->setName('id');
		$idType->setLabel('id');
		$idType->setAttributeClass($namespace . 'IntegerAttribute');
		$idType->setFormType('number');
		$rootType->addChildren($idType);

		$booleanType = new Type();
		$booleanType->setName('checkbox');
		$booleanType->setLabel('checkbox');
		$booleanType->setAttributeClass($namespace . 'BooleanAttribute');
		$booleanType->setFormType('checkbox');
		$rootType->addChildren($booleanType);

		$fieldsetType = new Type();
		$fieldsetType->setName('fieldset');
		$fieldsetType->setLabel('fieldset');
		$fieldsetType->setAttributeClass($namespace . 'CollectionAttribute');
		$fieldsetType->setFormType('form');
		$fieldsetType->setDataClass($namespace.'CollectionAttribute');

		$phoneType = new Type();
		$phoneType->setName('phone');
		$phoneType->setLabel('phone');
		$phoneType->setAttributeClass($namespace . 'StringAttribute');
		$phoneType->setFormType('text');
		$fieldsetType->addChildren($phoneType);

		$rootType->addChildren($fieldsetType);

		return array($rootType, $usernameType, $idType, $booleanType, $fieldsetType, $phoneType);
	}

	public static function buildType(ContainerInterface $container)
	{
		list($rootType, $usernameType, $idType, $booleanType, $fieldsetType, $phoneType) = self::buildDemoType();

		$data = new CollectionAttribute();
		$data->setType($rootType);

		$attributeClass = $usernameType->getAttributeClass();
		$usernameAttribute = new $attributeClass();
		$usernameAttribute->setValue('testusername');
		$usernameAttribute->setType($usernameType);
		$data->username = $usernameAttribute;

		$attributeClass = $idType->getAttributeClass();
		$idAttribute = new $attributeClass();
		$idAttribute->setValue(123123);
		$idAttribute->setType($idType);
		$data->id = $idAttribute;


		$attributeClass = $booleanType->getAttributeClass();
		/** @var BooleanAttribute $booleanAttribute */
		$booleanAttribute = new $attributeClass();
		$booleanAttribute->setValue(true);
		$booleanAttribute->setType($booleanType);
		$data->checkbox = $booleanAttribute;

		$attributeClass = $fieldsetType->getAttributeClass();
		/** @var CollectionAttribute $fieldsetAttribute */
		$fieldsetAttribute = new $attributeClass();
		$fieldsetAttribute->setType($fieldsetType);

		$attributeClass = $phoneType->getAttributeClass();
		$phoneTypeAttribute = new $attributeClass();
		$phoneTypeAttribute->setValue('+36203727668');
		$phoneTypeAttribute->setType($phoneType);
		$fieldsetAttribute->phone = $phoneTypeAttribute;

		$data->fieldset = $fieldsetAttribute;

		$formFactory = $container->get('form.factory');
		$rootForm = $formFactory->createBuilder(new AttributeBasedType($rootType), $data, $rootType->buildFormOptions());

		return $rootForm->getForm();
	}

	protected function loadDataFixtures()
	{
	}


	public function testStructureCanBeCreated()
	{
		$container = static::$kernel->getContainer();
		$form = self::buildType($container);
		$form->submit(array(
			'username' => 'alma',
			'checkbox' => false,
			'id' => 234,
		), false);
		$em = $container->get('doctrine.orm.default_entity_manager');
		$em->persist($form->getData());
		$em->flush();

		$view = $form->createView();

		$html = static::$kernel->getContainer()->get('twig')->render('CoreAttributeBundle:Test:form.html.twig', array(
			'form' => $view,
		));

		file_put_contents('../test_output.html', $html);
//		exec('open ../test_output.html');//osx only
	}

	public static function addChild(FormBuilder $root, Type $type)
	{
		$container = static::$kernel->getContainer();
		$formFactory = $container->get('form.factory');
		$childForm = $root->create($type->getName(), $type->getFormType(), $type->buildFormOptions());
		$root->add($childForm);

		$attributeToValueTransformer = new AttributeToValueTransformer();
		$attributeToValueTransformer->setType($type);
		$valueToAttributeTransformer = new ReversedTransformer($attributeToValueTransformer);

//		$childForm->addModelTransformer($attributeToValueTransformer);
		$childForm->addViewTransformer($attributeToValueTransformer);

		return $childForm;
	}
}