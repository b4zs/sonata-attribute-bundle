<?php

namespace Core\AttributeBundle\Entity;


use Sonata\MediaBundle\Model\GalleryInterface;

class GalleryAttribute extends Attribute
{
	/** @var GalleryInterface */
	protected $galleryValue;

    public function __construct()
    {
    }

    /**
	 * @return GalleryInterface
	 */
	public function getValue()
	{
		return $this->getGalleryValue();
	}

	/**
	 * @param GalleryInterface $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->galleryValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && !$value instanceof GalleryInterface) {
			throw $this->createValueAssertionException('Sonata\MediaBundle\Model\GalleryInterface', $value);
		}
	}

    public function getGalleryValue()
    {
        return $this->galleryValue;
    }

    public function setGalleryValue($galleryValue)
    {
        $this->galleryValue = $galleryValue;
    }

}