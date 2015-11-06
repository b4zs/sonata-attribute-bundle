<?php


namespace Core\AttributeBundle\Entity;


class StringAttribute extends Attribute
{
	/** @var  string */
	protected $stringValue;

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->stringValue;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->stringValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && !is_string($value)) {
			throw $this->createValueAssertionException('a string', $value);
		}
	}
}