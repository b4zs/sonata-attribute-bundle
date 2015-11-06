<?php


namespace Core\AttributeBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Type
{
	private $id;

	private $name;

	private $label;

	private $position = 0;

	private $attributeClass = 'Core\\AttributeBundle\\Entity\\StringAttribute';

	private $dataClass = null;

	private $formType;

	private $formOptions = array();

	/** @var  Collection */
	private $children;

	/** @var  Type|null */
	private $parent;

	function __construct()
	{
		$this->children = new ArrayCollection();
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		if (null === $this->label) {
			$this->setLabel($name);
		}
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($label)
	{
		$this->label = $label;
	}

	public function getPosition()
	{
		return $this->position;
	}

	public function setPosition($position)
	{
		$this->position = $position;
	}

	public function getAttributeClass()
	{
		return $this->attributeClass;
	}

	public function setAttributeClass($attributeClass)
	{
		if (!class_exists($attributeClass)) {
			throw new \InvalidargumentException('Class does not exists: '.$attributeClass);
		}
		$this->attributeClass = $attributeClass;
	}

	public function getFormType()
	{
		return $this->formType;
	}

	public function setFormType($formType)
	{
		$this->formType = $formType;
	}

	public function getFormOptions()
	{
		return $this->formOptions;
	}

	public function setFormOptions($formOptions)
	{
		$this->formOptions = $formOptions;
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function setChildren($children)
	{
		$this->children = $children;
	}

	public function addChildren(Type $type)
	{
		$type->setParent($this);
		$this->getChildren()->add($type);
	}

	public function buildFormOptions()
	{
		return array_merge(
			$this->getFormOptions(),
			array(
				'label'         => $this->getLabel(),
				'data_class'    => $this->getDataClass(),
			)
		);
	}

	public function getId()
	{
		return $this->id;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function setParent(Type $parent = null)
	{
		$this->parent = $parent;
	}

	public function getDataClass()
	{
		return $this->dataClass;
	}

	public function setDataClass($dataClass)
	{
		$this->dataClass = $dataClass;
	}

//	public static function create($name, $label = null, $attributeClass = null, $dataClass = null, $formType = null, array $formOptions = array())
//	{
//		$instance = new static();
//		$instance->setName($name);
//		if ($label) $instance->setLabel($label);
//		if ($attributeClass) $instance->setAttributeClass($attributeClass);
//		if ($dataClass) $instance->setDataClass($dataClass);
//		if ($formType) $instance->setFormType($formType);
//		if ($formOptions) $instance->setFormOptions($formOptions);
//
//		return $instance;
//	}

	function __toString()
	{
		return 'Type#'.$this->getId().'('.$this->getName().')';
	}

	public function buildPath()
	{
		$path = array($current = $this);
		while ($current = $current->getParent()) {
			$path[] = $current;
		}
		return array_reverse($path);
	}


}