<?php

namespace Core\AttributeBundle\Controller;

use Core\AttributeBundle\Admin\TypeAdmin;
use Core\AttributeBundle\Entity\Type;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TypeAdminController extends CRUDController
{
	public function createAction()
	{
		$request = $this->container->get('request');
		$isGet = $request->isMethod('GET');
		$preset = $request->get('preset');
		$parent = $request->get('parent');
		if (null === $preset && $isGet) {
			/** @var TypeAdmin $admin */
			$admin = $this->admin;

			$optionsProviderChain = $this->get('core_attribute.form_type_options_provider.provider_chain');
			$presetNames = array_keys($optionsProviderChain->getProviders());

			if(null === $parent){
				$presetNames = array_intersect(
					array('form'),
					$presetNames
				);
			}

			return $this->render('CoreAttributeBundle:TypeAdmin:create_preset_selector.html.twig', array(
				'presets' => array_combine($presetNames, $presetNames),
				'action' => 'create',
				'object' => $admin->getNewInstance(),
			));
		} else {
			return parent::createAction();
		}
	}

	public function listAction()
	{
		$parentId = $this->admin->getPersistentParameter('parent');
		if(null === $parentId){
			return parent::listAction();
		}

		if (false === $this->admin->isGranted('LIST')) {
			throw new AccessDeniedHttpException();
		}

		$datagrid = $this->admin->getDatagrid();
		$formView = $datagrid->getForm()->createView();

		$data = $datagrid->getQuery()->execute(array());
		$treeNodes = array_map(array($this, 'buildBlockData'), $data);
		$treeNodes = $this->buildNodesHierarchy($treeNodes, $parentId);

//		var_dump($treeNodes);
//		die();

		// set the theme for the current Admin Form
		$this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

		$dynamicEventListenerAdmin = $this->container->has('core_dynamic_event_listener.admin.dynamic_event_listener')? $this->get('core_dynamic_event_listener.admin.dynamic_event_listener') : null;

		return $this->render($this->admin->getTemplate('list'), array(
			'action'     => 'list',
			'form'       => $formView,
			'datagrid'   => $datagrid,
			'csrf_token' => $this->getCsrfToken('sonata.batch'),
			'tree_nodes'  => $treeNodes,
			'dynamic_event_listener_admin' => $dynamicEventListenerAdmin,
		));
	}

	private function buildBlockData(Type $type){

		return array(
			'id' 		=> $type->getId(),
			'label' 	=> $type->getLabel()?:$type->getName(),
			'form_type' => $type->getFormType(),
			'parent' 	=> $type->getParent()?$type->getParent()->getId():null,
			'submission_event_name' => 'dynamic_form_submission.'.$type->getName(),
		);

	}

	private function buildNodesHierarchy($input, $rootId = null){
		$nodes = array();
		$result = array();


		foreach ($input as &$node) {
			$nodes[$node['id']] =& $node;
		}

		foreach ($nodes as &$node) {
			if ($node['parent']) {
				if (isset($nodes[$node['parent']])) {
					$nodes[$node['parent']]['items'][] =& $node;
				} else {
					//?
				}
			}
		}

		foreach ($nodes as &$node) {
//			if (!$node['parent']) {
//				if($rootId !== null && $node['id'] == $rootId){
//					$result[] =& $node;
//				}elseif($rootId === null){
//					$result[] =& $node;
//				}else{
//					//?
//				}
//			}

			if (!$node['parent'] && $rootId === null) {
				$result[] =& $node;
			}elseif($rootId !== null && $node['id'] == $rootId){
				$result[] =& $node;
			}
		}

		return $result;
	}

}
