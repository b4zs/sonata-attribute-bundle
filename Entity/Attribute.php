<?php


namespace Core\AttributeBundle\Entity;


abstract class Attribute
{
	private $id;

	/** @var  Type */
	private $type;

	/** @var  CollectionAttribute */
	protected $parent;

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
}