<?php

namespace Core\AttributeBundle\Admin;


use Core\AttributeBundle\Entity\Type;
use Doctrine\ORM\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class TypeAdmin extends Admin
{

    protected $maxPerPage = 99999;

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }


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
            ->add('label', null, array(
                'required' => true,
            ))
            ->add('name', null, array(
                'label' => 'Technical name',
                'sonata_help' => 'must be unique',
                'read_only' => null !== $this->getSubject()->getId(),
            ))
        ;

        if($object){
            $options = $object->getFormType() == 'form'?array('required' => false):array();
            $formMapper->add('parent', 'sonata_type_model_list', $options);

            if(null !== $object->getParent()){
                $formMapper->add('position', 'integer');
            }

        }

        $formMapper->end();

//        $formMapper
//            ->add('attributeClass', 'text', array())
//            ->add('dataClass', 'text', array(
//                'required' => false,
//            ))
//            ->add('formType', 'choice', array(
//                'choices' => $this->getAvailableFormTypes(),
//            ))
//            ->end();

        if($object && $object->getFormType()){
            $formMapper->with('Form options', array('class' => 'col-md-6'))
                ->add('formOptions', 'form_options', array(
                    'form_type' => $object->getFormType(),
                    'required' => false,
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

    /**
     * @param Type $object
     */
    private function addLabelToOptions($object)
    {
        $options = $object->getFormOptions();
        if (array_key_exists('label', $options)) {
            $options['label'] = $object->getLabel();
            $object->setFormOptions($options);
        }
    }

    public function prePersist($object)
    {
        if ($object instanceof Type) {
            $this->addLabelToOptions($object);
            $object->setChildren($object->getChildren());
        }
    }

    public function preUpdate($object)
    {
        if ($object instanceof Type) {
            $this->addLabelToOptions($object);
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
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        $query->orderBy('o.position');

        if (null === $this->getPersistentParameter('parent')) {
            $query
                ->andWhere('o.parent IS NULL');
        } else {
            //?
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
