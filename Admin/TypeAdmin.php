<?php

namespace Core\AttributeBundle\Admin;


use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\AttributeBasedType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class TypeAdmin extends Admin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('label')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('label');

        if($this->getPersistentParameter('parent')){
            $listMapper
                ->add('formType')
                ->add('formOptions', null, array(
                    'template' => 'CoreAttributeBundle:TypeAdmin:list_yaml_field.html.twig',
                ));
        }

        $listMapper
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                    'submissions' => array(
                        'template' => 'CoreAttributeBundle:TypeAdmin:list__action_submissions.html.twig'
                    ),
                    'fields' => array(
                        'template' => 'CoreAttributeBundle:TypeAdmin:list__action_fields.html.twig'
                    ),
                )
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $isChildrenTableForm = false;
        /** @var FieldDescription $fieldDescription */
        $fieldDescription = $formMapper->getFormBuilder()->getOption('sonata_field_description');
        if ($fieldDescription instanceof FieldDescription && $fieldDescription->getAssociationMapping()) {
            $associationMapping = $fieldDescription->getAssociationMapping();
            $isChildrenTableForm = $associationMapping['fieldName'] === 'children';
        }

        $object = $this->getSubject();

        $formMapper
            ->with('General', array('class' => 'col-md-6'))
            ->add('name')
            ->add('label', null, array(
                'required' => false,
            ));

        if($object && $object->getFormType() != 'dynamic_form'){
            $formMapper->add('parent', 'sonata_type_model_list');
        }

        $formMapper->add('attributeClass', 'text', array())
            ->add('dataClass', 'text', array(
                'required' => false,
            ))
            ->add('formType', 'choice', array(
                'choices' => $this->getAvailableFormTypes(),
            ))
            ->end();

        if($object && $object->getFormType()){
            $formMapper->with('Form options', array('class' => 'col-md-6'))
                ->add('formOptions', 'form_options', array(
                    'form_type' => $object->getFormType(),
                ));
        }

        $formMapper->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('label')
            ->add('position')
            ->add('attributeClass')
            ->add('dataClass')
            ->add('formType')
            ->add('formOptions')
        ;
    }

    private function getAvailableFormTypes()
    {
        $optionsProviderChain = $this->getConfigurationPool()->getContainer()->get('core_attribute.form_type_options_provider.provider_chain');
        $types = array_keys($optionsProviderChain->getProviders());

        return array_combine($types, $types);
    }

    public function getNewInstance()
    {

        if ($this->hasRequest() && $preset = $this->getRequest()->get('preset')) {
            $typeFactory = $this->configurationPool->getContainer()->get('core_attribute.factory.type');
            $object = $typeFactory->create($preset);

            foreach ($this->getExtensions() as $extension) {
                $extension->alterNewInstance($this, $object);
            }
        } else {
            return parent::getNewInstance();
        }

        if ($this->hasRequest() && $parentId = $this->getRequest()->get('parent')) {
            $parent = $this->getModelManager()->find($this->getClass(), $parentId);
            $object->setParent($parent);

        }

        return $object;
    }

    public function toString($object)
    {
        if ($object instanceof Type && $object->getLabel()) {
            return $object->getLabel();
        } else {
            return parent::toString($object);
        }
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'show': return 'CoreAttributeBundle:TypeAdmin:show.html.twig';
            case 'edit': return 'CoreAttributeBundle:TypeAdmin:edit.html.twig';
            case 'list': return 'CoreAttributeBundle:TypeAdmin:list.html.twig';
            default:     return parent::getTemplate($name);
        }
    }

    public function buildFormByType($type, $data = null, array $options = array())
    {
        $containerInterface = $this->getConfigurationPool()->getContainer();
        $formFactory = $containerInterface->get('form.factory');
        $form = $formFactory->create(new AttributeBasedType($type), $data, $options);

        if ($this->hasRequest()) {
            $form->handleRequest($this->getRequest());
        }
        return $form;
    }

    public function prePersist($object)
    {
        if ($object instanceof Type) {
            $object->setChildren($object->getChildren());
        }
    }

    public function preUpdate($object)
    {
        if ($object instanceof Type) {
            $object->setChildren($object->getChildren());
        }
    }

    public function getPersistentParameters()
    {
        $d = parent::getPersistentParameters();

        $request = $this->getRequest();
        $params = array('uniqid', 'code', 'pcode', 'puniqid','parent', /*'preset'*/);
        foreach ($params as $key) {
            if ($val = $request->get($key)) {
                $d[$key] = $val;
            }
        }

        return $d;
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        if ($parentId = $this->getPersistentParameter('parent')) {
            $query
                ->andWhere('o.parent = :parent_id')
                ->setParameter('parent_id', $parentId);
        } else {
            $query
                ->andWhere('o.parent IS NULL');
        }

        return $query;
    }


    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {

        /** @var Type|null $subject */
        $subject = $this->getSubject();

        if($subject && $subject->getId()) {

            //if the current form type does not have a parent,
            //it means that it is a root form, and can have submissions
            if(!$subject->getParent()){
                /** @var FormSubmissionAdmin $formSubmissionAdmin */
                $formSubmissionAdmin = $this->getRoot()->getChild('core_attribute.admin.form_submission');
                if ($formSubmissionAdmin && $formSubmissionAdmin->isGranted('LIST')) {
                    $submissionsListUrl = $formSubmissionAdmin->generateUrl('list', $parameter = array());

                    $menu->addChild('submissions', array(
                        'uri' => $submissionsListUrl,
                        'label' => sprintf('Submissions of %s', $subject->getLabel()?:$subject->getName()),
                    ));
                }
            }

            if($subject->getChildren()->count()){
                if ($this->isGranted('LIST')) {
                    $typeAdminUrl = $this->generateUrl('list', array('parent' =>  $subject->getId()));

                    $menu->addChild('fields', array(
                        'uri' => $typeAdminUrl,
                        'label' => sprintf('Fields of %s', $subject->getLabel()?:$subject->getName()),
                    ));
                }
            }
        }

    }

    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        $subject = $this->getSubject();
        /** @var MenuItemInterface $result */
        $result = parent::buildBreadcrumbs($action, $menu);

        $parentId = $this->getPersistentParameter('parent');
        $currentAdmin = $this->getCurrentChildAdmin()?:$this;

        if($currentAdmin instanceof TypeAdmin){
            $result = $this->resetListBreadcrumbItemUri($result);
            if(($subject && $subject instanceof Type) || $parentId){
                if ($this->getSubject()) {
                    $subject = $this->getSubject();
                    $menu = $this->breadcrumbs[$action]->getParent();
                } else {
                    $subject = $this->getModelManager()->find($this->getClass(), $parentId);
                    $menu = $this->breadcrumbs[$action];
                }

                foreach ($subject->buildPath() as $pathType) {
                    $menu = $menu->addChild(
                        $pathType->getId() ? ($pathType->getLabel()?:$pathType->getName()) : '+',
                        array('uri' => $pathType->getId() ? $this->generateObjectUrl('edit', $pathType) : null)
                    );
                }

                if ($parentId && !$this->getSubject()) { // subfield listing
                    $menu = $menu->addChild(
                        'Fields',
                        array('uri' => $this->generateUrl('list', array('parent' => $subject->getId())))
                    );
                }

                $result = $menu;
            }
        }

        return $result;
    }

    protected function resetListBreadcrumbItemUri($result)
    {
        $rootChildren = $result->getRoot()->getChildren();
        $dashboardChildren = current($rootChildren)->getChildren();
        $listItem = current($dashboardChildren);
        $listItem->setUri($this->generateUrl('list', array('parent' => null,)));
        return $listItem;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUrl($name, array $parameters = array(), $absolute = false)
    {
        if($name == "create" && isset($parameters['uniqid']) && $this->hasRequest() && $preset = $this->getRequest()->get('preset')){
            $parameters = array_merge(array('preset' => $preset), $parameters);
        }
        return parent::generateUrl($name, $parameters, $absolute);
    }


}
