<?php

namespace Core\AttributeBundle\Entity;

use Sonata\MediaBundle\Model\MediaInterface;

class MediaAttribute extends Attribute
{
	/** @var MediaInterface */
	protected $mediaValue;

	/**
	 * @return MediaInterface
	 */
	public function getValue()
	{
		return $this->mediaValue;
	}

	/**
	 * @param MediaInterface $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->mediaValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && !$value instanceof MediaInterface) {
			throw $this->createValueAssertionException('a Sonata\MediaBundle\Model\MediaInterface', $value);
		}
	}

}