<?php


namespace Core\AttributeBundle\Entity;


class BooleanAttribute extends Attribute
{
	/** @var  boolean */
	protected $booleanValue;

	/**
	 * @return boolean
	 */
	public function getValue()
	{
		return $this->booleanValue;
	}

	/**
	 * @param boolean $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->booleanValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && !is_bool($value)) {
			throw $this->createValueAssertionException('a boolean', $value);
		}
	}
}