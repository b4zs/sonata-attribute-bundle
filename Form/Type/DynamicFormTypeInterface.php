<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

interface DynamicFormTypeInterface extends FormTypeInterface
{

	public function getOptions();

}