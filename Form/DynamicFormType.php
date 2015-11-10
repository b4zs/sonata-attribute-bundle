<?php

namespace Core\AttributeBundle\Form;

use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DataTransformer\AttributeToValueTransformer;
use Core\AttributeBundle\FormTypeOptionsProvider\ProviderChain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraints\NotBlank;

class DynamicFormType extends AbstractType
{
	/** @var Type */
	private $type;

	private $providerChain;

	function __construct(ProviderChain $providerChain, Type $type){
		$this->providerChain = $providerChain;
		$this->type = $type;
	}

	/**
	 * @param Type $type
	 */
	public function setType($type)
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

			$builder->add($name, new DynamicFormType($this->providerChain, $child), array(
				'data'  => $childData,
			));
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
		$provider = $this->providerChain->getProvider($this->type->getFormType());
		$options = $provider->appendConstraints($this->type->getFormOptions());
		$resolver->setDefaults($options);
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