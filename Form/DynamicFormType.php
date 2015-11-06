<?php

namespace Core\AttributeBundle\Form;

use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DataTransformer\AttributeToValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DynamicFormType extends AbstractType
{
	/** @var Type */
	private $type;

	function __construct(Type $type)
	{
		$this->type = $type;
	}


	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$data = isset($options['data']) ? $options['data'] : null;
		$pa = null === $data ? null : new PropertyAccessor();

		foreach ($this->type->getChildren() as $child) {
			$name = $child->getName();
			$childData = $pa === null ? null : $pa->getValue($data, $name);

			$builder->add($name, new DynamicFormType($child), array(
				'data'  => $childData,
			));
		}


		//TODO: ?!?! do we need it? shouldn't each attributeValue has own name field?
		if ($data instanceof CollectionAttribute) {
			foreach ($data->getValue() as $childAttribute) {
				//TODO:
			}
		}

		$attributeToValueTransformer = new AttributeToValueTransformer();
		$attributeToValueTransformer->setType($this->type);
		$builder->addModelTransformer($attributeToValueTransformer);
	}

	/**
	 * Configures the options for this type.
	 *
	 * @param OptionsResolver $resolver The resolver for the options.
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults($this->type->buildFormOptions());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return $this->type->getFormType();
	}

	public function getName()
	{
		return 'attr_'.$this->type->getName();
	}



}