<?php

namespace Core\AttributeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OpenButtonType extends AbstractType
{

	public function getName()
	{
		return 'core_attribute_open_btn';
	}

	public function getParent()
	{
		return 'text';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'virtual'       => true,
			'inherit_data'  => true,
			'required'      => false,
			'label'         => 'Open',
			'attr'          => array(
				'class' => 'btn btn-info',
			),
		));
	}


}