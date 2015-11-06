<?php


namespace Core\AttributeBundle\Entity;


class FloatAttribute extends Attribute
{
	/** @var float */
	protected $floatValue;

	/**
	 * @return float
	 */
	public function getValue()
	{
		return $this->floatValue;
	}

	/**
	 * @param float $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->floatValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && false === filter_var($value, FILTER_VALIDATE_FLOAT)) {
			throw $this->createValueAssertionException('a float', $value);
		}
	}

}