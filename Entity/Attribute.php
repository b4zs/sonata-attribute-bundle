<?php


namespace Core\AttributeBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;

abstract class Attribute
{
	private $id;

	/** @var  Type */
	private $type;

	/** @var  CollectionAttribute */
	protected $parent;

	function __toString()
	{
		return (string)$this->getValue();
	}

	public function getId()
	{
		return $this->id;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType(Type $type)
	{
		$this->type = $type;
	}

	abstract  public function getValue();

	abstract public function setValue($value);

	public function getParent()
	{
		return $this->parent;
	}

	public function setParent(CollectionAttribute $parent = null)
	{
		$this->parent = $parent;
	}

	public function assertValue($value)
	{
		throw new \RuntimeException('This method should be overridden');
	}

	protected function createValueAssertionException($expected, $actual)
	{
		$paramPresentation = is_object($actual) ? get_class($actual) : json_encode($actual);
		return new \InvalidArgumentException('Parameter 1 must be '.$expected.', ' . $paramPresentation . ' given');
	}

	public function serialize()
	{
		return $this->getValue();
	}

    /**
     * When an object is cloned, PHP 5 will perform a shallow copy of all of the object's properties.
     * Any properties that are references to other variables, will remain references.
     * Once the cloning is complete, if a __clone() method is defined,
     * then the newly created object's __clone() method will be called, to allow any necessary properties that need to be changed.
     * NOT CALLABLE DIRECTLY.
     *
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone()
    {
        $this->id = null;
    }


}
