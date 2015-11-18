<?php

namespace Core\AttributeBundle\Controller;

use Core\AttributeBundle\Admin\TypeAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

class TypeAdminController extends CRUDController
{
	public function createAction()
	{
		$request = $this->container->get('request');
		$isGet = $request->isMethod('GET');
		$preset = $request->get('preset');
		if (null === $preset && $isGet) {
			/** @var TypeAdmin $admin */
			$admin = $this->admin;

			$optionsProviderChain = $this->get('core_attribute.form_type_options_provider.provider_chain');
			$presetNames = array_keys($optionsProviderChain->getProviders());

			return $this->render('CoreAttributeBundle:TypeAdmin:create_preset_selector.html.twig', array(
				'presets' => array_combine($presetNames, $presetNames),
				'action' => 'create',
				'object' => $admin->getNewInstance(),
			));
		} else {
			return parent::createAction();
		}
	}

}
