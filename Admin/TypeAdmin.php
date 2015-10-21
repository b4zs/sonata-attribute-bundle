<?php

namespace Core\AttributeBundle\Admin;


use Core\AdminBundle\Form\Type\ActionsType;
use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\AttributeBasedType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Knp\Menu\MenuItem;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class TypeAdmin extends Admin
{
    const ATTR_ATTR         = 'Core\\AttributeBundle\\Entity\\Attribute';
    const ATTR_COLLECTION   = 'Core\\AttributeBundle\\Entity\\CollectionAttribute';
    const ATTR_STRING       = 'Core\\AttributeBundle\\Entity\\StringAttribute';
    const ATTR_INTEGER      = 'Core\\AttributeBundle\\Entity\\IntegerAttribute';
    const ATTR_BOOLEAN      = 'Core\\AttributeBundle\\Entity\\BooleanAttribute';

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('label')
            ->add('position')
            ->add('attributeClass')
            ->add('valueClass')
            ->add('formType')
            ->add('formOptions')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('label')
//            ->add('position')
//            ->add('attributeClass')
//            ->add('valueClass')
            ->add('formType')
            ->add('formOptions', null, array(
                'template' => 'CoreAttributeBundle:TypeAdmin:list_yaml_field.html.twig',
            ))
//            ->add('parent')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
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


        $formMapper
            ->with('General', array('class' => 'col-md-6'))
            ->add('name')
            ->add('label', null, array(
                'required' => false,
            ))
            ->add('attributeClass', 'choice', array(
                'choices' => $this->fetchAttributeClassChoices(),
            ))
            ->add('valueClass', 'text', array(
                'required' => false,
            ))
            ->add('formType', 'choice', array(
                'choices' => $this->getAvailableFormTypes(),
            ))
            ->end()->with('Form options', array('class' => 'col-md-6'))
            ->add('formOptions', 'yaml_array')
            ->add('parent', 'sonata_type_model_list')
            ->end()
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
            ->add('valueClass')
            ->add('formType')
            ->add('formOptions')
        ;
    }

    private function fetchAttributeClassChoices()
    {
        $entityManager = $this->configurationPool->getContainer()->get('doctrine.orm.entity_manager');
        $classMetadata = $entityManager->getClassMetadata(self::ATTR_ATTR);
        $choices = $classMetadata->discriminatorMap;
        unset($choices['attribute']);

        $choices = array_map(function($t){ return str_replace('attribute', '', $t);}, array_flip($choices));

        return $choices;
    }

    private function getAvailableFormTypes()
    {
        $types = array(
            'form',
            'text',
            'textarea',
            'integer',
            'checkbox'
        );

        return array_combine($types, $types);
    }

    public function getPresets()
    {
        return array(
            'form'      => Type::create('', '', self::ATTR_COLLECTION, self::ATTR_COLLECTION, 'form', array()),
            'text'      => Type::create('', '', self::ATTR_STRING, null, 'text', array()),
            'integer'   => Type::create('', '', self::ATTR_INTEGER, null, 'integer', array()),
            'boolean'   => Type::create('', '', self::ATTR_BOOLEAN, null, 'checkbox', array('required' => false,)),
        );
    }

    public function getNewInstance()
    {
        if ($this->hasRequest() && $preset = $this->getRequest()->get('preset')) {
            $presets = $this->getPresets();
            if (isset($presets[$preset])) {
                $object = $presets[$preset];
                foreach ($this->getExtensions() as $extension) {
                    $extension->alterNewInstance($this, $object);
                }
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

    public function getSubject()
    {
        return parent::getSubject();
    }

    public function toString($object)
    {
        if ($object instanceof Type && $object->getId()) {
            return $object->getName();
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
        $params = array('uniqid', 'code', 'pcode', 'puniqid','parent');
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
//            die('parent is null');
            $query
                ->andWhere('o.parent IS NULL');
        }

        return $query;
    }


    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        list($subject, $parent, $isCollection) = $this->detectContext();

        if ($parent) {
            if ('edit' === $action && $subject) {
                $menu->addChild('back_to_parent', array(
                    'uri' => $this->generateUrl('list', array('parent' => $parent->getId(), 'uniqid' => null,)),
                    'label' => '« Fields of '.json_encode($parent->getName()),
                ));
            } else {
                $menu->addChild('back_to_parent', array(
                    'uri' => $this->generateUrl('edit', array('id' => $parent->getId(), 'parent' => null, 'uniqid' => null,)),
                    'label' => '« Edit '.json_encode($parent->getName()),
                ));
            }
        }

        if ('edit' === $action && $isCollection) {
            $menu->addChild('child_forms', array(
                'uri'       => $this->generateUrl('list', array('parent' => $subject->getId())),
                'label'     => 'Fields of '.json_encode($subject->getName()).' »',
            ));
        }


    }

    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        /** @var MenuItem $result */
        $result = parent::buildBreadcrumbs($action, $menu);
        $this->resetListBreadcrumbItemUri($result);

        list($subject, $parent, $isCollection) = $this->detectContext();


        if ($subject || $parent) {
            /** @var Type $currentType */
            if ($this->getSubject()) {
                $currentType = $this->getSubject();
                $menu = $this->breadcrumbs[$action]->getParent();
            } else {
                $currentType = $parent;
                $menu = $this->breadcrumbs[$action];
            }

            /** @var Type $pathType */
            foreach ($currentType->buildPath() as $pathType) {
                $menu = $menu->addChild(
                    $pathType->getId() ? $pathType->getName() : '+',
                    array('uri' => $pathType->getId() ? $this->generateObjectUrl('edit', $pathType) : null)
                );
            }

            if ($parent && !$this->getSubject()) { // subfield listing
                $menu = $menu->addChild(
                    'Fields',
                    array('uri' => $this->generateUrl('list', array('parent' => $parent->getId())))
                );
            }

            $result = $menu;
        }

        return $result;
    }

    protected function detectContext()
    {
        $subject = null;
        /** @var Type $parent */
        $parent = null;
        $isCollection = false;

        if ($this->getSubject()) {
            $subject = $this->getSubject();
            $isCollection = $subject->getFormType() === 'form';
            $parent = $subject->getParent();
            return array($subject, $parent, $isCollection);
        } elseif ($parentId = $this->getPersistentParameter('parent')) {
            $parent = $this->getModelManager()->find($this->getClass(), $parentId);
            return array($subject, $parent, $isCollection);
        }

        return array($subject, $parent, $isCollection);
    }

    public function getParentType()
    {
        list($subject, $parent, $isCollection) = $this->detectContext();

        return $parent;
    }

    public function generateUrl($name, array $parameters = array(), $absolute = false)
    {
        if ($name === 'create' && $this->hasSubject() && $this->getSubject()->getFormType() === 'form' && !array_key_exists('parent', $parameters)) {
            $parameters['parent'] = $this->getSubject()->getId();
        }

        return parent::generateUrl($name, $parameters, $absolute);
    }

    protected function resetListBreadcrumbItemUri($result)
    {
        $rootChildren = $result->getRoot()->getChildren();
        $dashboardChildren = current($rootChildren)->getChildren();
        $listItem = current($dashboardChildren);
        $listItem->setUri($this->generateUrl('list', array('parent' => null,)));
    }


}
