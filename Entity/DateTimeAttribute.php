<?php


namespace Core\AttributeBundle\Entity;


class DateTimeAttribute extends Attribute
{
	/** @var \DateTime */
	protected $dateTimeValue;

	/**
	 * @return \DateTime
	 */
	public function getValue()
	{
		return $this->dateTimeValue;
	}

	/**
	 * @param \DateTime $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->dateTimeValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && !$value instanceof \DateTime) {
			throw $this->createValueAssertionException('a DateTime', $value);
		}
	}

}