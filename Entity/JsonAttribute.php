<?php


namespace Core\AttributeBundle\Entity;


class JsonAttribute extends Attribute
{
	/** @var string */
	protected $jsonValue;

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->jsonValue;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->jsonValue = $value;
	}

	public function assertValue($value)
	{
		//todo write validator
//		if ($value && false === filter_var($value, FILTER_VALIDATE_FLOAT)) {
//			throw $this->createValueAssertionException('a float', $value);
//		}
	}

}