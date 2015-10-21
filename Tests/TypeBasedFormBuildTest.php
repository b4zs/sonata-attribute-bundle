<?php


namespace Core\AttributeBundle\Tests;


use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DataTransformer\AttributeToValueTransformer;
use Core\ToolsBundle\Tests\ContainerAwareTest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\ReversedTransformer;

class TypeBasedFormBuildTest extends ContainerAwareTest
{
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
		$rootType->setValueClass($namespace.'CollectionAttribute');

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

		return array($rootType, $usernameType, $idType);
	}

	public static function buildType(ContainerInterface $container)
	{
		list($rootType, $usernameType, $idType) = self::buildDemoType();


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


		$formFactory = $container->get('form.factory');
		$rootForm = $formFactory->createBuilder($rootType->getFormType(), $data, $rootType->buildFormOptions());
		$attributeToValueTransformer = new AttributeToValueTransformer();
		$attributeToValueTransformer->setType($rootType);
		$rootForm->addModelTransformer($attributeToValueTransformer);

		self::addChild($rootForm, $usernameType);
		self::addChild($rootForm, $idType);

		return $rootForm->getForm();
	}

	protected function loadDataFixtures()
	{
	}


	public function testStructureCanBeCreated()
	{
		$form = self::buildType($this->getContainer());
		$form->submit(array(
			'username' => 'alma',
		));
		var_dump($form->getData());
	}

	public static function addChild(FormBuilder $root, Type $type)
	{
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