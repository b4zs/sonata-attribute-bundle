<?php

namespace Core\AttributeBundle\Entity;

use Sonata\ClassificationBundle\Model\Category;

class CategoryAttribute extends Attribute
{
    /** @var Category */
	protected $categoryValue;

    public function __construct()
    {
    }


    /**
	 * @return Category
	 */
	public function getValue()
	{
		return $this->categoryValue;
	}

	/**
	 * @param Category $value
	 */
	public function setValue($value)
	{
		$this->assertValue($value);
		$this->categoryValue = $value;
	}

	public function assertValue($value)
	{
		if ($value && !$value instanceof Category) {
			throw $this->createValueAssertionException('a Sonata\ClassificationBundle\Model\Category', $value);
		}
	}

    public function getCategoryValue()
    {
        return $this->categoryValue;
    }

    public function setCategoryValue($categoryValue)
    {
        $this->categoryValue = $categoryValue;
    }

}