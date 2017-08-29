<?php

namespace Core\AttributeBundle\Controller;

use Core\AttributeBundle\Admin\FormSubmissionAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

class AttributeAdminController extends CRUDController
{
    public function createAction()
    {
        $request = $this->getRequest();

        if (!$request->get('type_id') && $request->isMethod('get')) {
            /** @var FormSubmissionAdmin $admin */
            $admin = $this->admin;

            return $this->render('CoreAttributeBundle:FormSubmissionAdmin:create_type_select.html.twig', array(
                'form'      => $this->createForm('form', array(), array())->createView(),
                'action'    => 'create',
                'object'    => $admin->getNewInstance(),
                'types'     => $admin->getDynamicTypes(),
            ));
        } else {
            return  parent::createAction();
        }
    }
}
