<?php


namespace Core\AttributeBundle\Entity;


class IntegerAttribute extends Attribute
{
	/** @var  integer */
	protected $integerValue;

	/**
	 * @return integer
	 */
	public function getValue()
	{
		return $this->integerValue;
	}

	/**
	 * @param integer $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->integerValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && false === filter_var($value, FILTER_VALIDATE_INT)) {
			throw $this->createValueAssertionException('an integer', $value);
		}
	}

}