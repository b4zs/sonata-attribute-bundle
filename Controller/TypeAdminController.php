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
			$presetNames = array_keys($admin->getPresets());

			return $this->render('CoreAttributeBundle:TypeAdmin:create_preset_selector.html.twig', array(
				'presets' => array_combine($presetNames, $presetNames),
				'action' => 'create',
				'object' => $admin->getNewInstance(),
			));
		} else {
			return parent::createAction();
		}
	}

	/**
	 * Show action.
	 *
	 * @param int|string|null $id
	 *
	 * @return Response
	 *
	 * @throws NotFoundHttpException If the object does not exist
	 * @throws AccessDeniedException If access is not granted
	 */
	public function showAction($id = null)
	{
		$id = $this->get('request')->get($this->admin->getIdParameter());

		$object = $this->admin->getObject($id);

		if (!$object) {
			throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
		}

		if (false === $this->admin->isGranted('VIEW', $object)) {
			throw new AccessDeniedException();
		}

		$this->admin->setSubject($object);

		$form = $this->admin->buildFormByType($object);

		return $this->render($this->admin->getTemplate('show'), array(
			'action'    => 'show',
			'object'    => $object,
			'elements'  => $this->admin->   getShow(),
			'form'      => $form->createView(),
		));
	}


}
