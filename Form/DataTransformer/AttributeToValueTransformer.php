<?php

namespace Core\AttributeBundle\Form\DataTransformer;


use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\Type;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class AttributeToValueTransformer implements DataTransformerInterface
{
	/** @var  Type */
	private $type;

	/**
	 * @param Attribute $value
	 * @return scalar|object
	 */
	public function transform($value)
	{
		if ($value instanceof CollectionAttribute) {
			$result =  $value;
		} elseif ($value instanceof Attribute) {
			$result = $value->getValue();
		} else {
			$result = null;
		}
//		var_dump(array('method' => __FUNCTION__, 'args' => func_get_args(), 'result' => $result));
		return $result;
	}

	/**
	 * @param scalar|object
	 * @return Attribute
	 */
	public function reverseTransform($value)
	{
//		var_dump(array('method' => __FUNCTION__, 'args' => func_get_args()));

		if ($value instanceof CollectionAttribute) {
			$value->setType($this->type);
			return $value;
		}

		if ($value instanceof Attribute) {
			throw new TransformationFailedException('Parameter 1 must not be an Attribute');
		}


		$className = $this->type->getAttributeClass();
		/** @var Attribute $attribute */
		$attribute = new $className();
		$attribute->setType($this->type);
		$attribute->setValue($value);

		return $attribute;
	}

	/**
	 * @return Type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param Type $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}


}