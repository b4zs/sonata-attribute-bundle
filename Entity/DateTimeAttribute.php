<?php


namespace Core\AttributeBundle\Entity;


class DateTimeAttribute extends Attribute
{
	/** @var \DateTime */
	protected $dateTimeValue;

	function __toString()
	{
		if($this->getValue() instanceof \DateTime){
			return $this->getValue()->format('r');
		}

		return '';
	}

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
