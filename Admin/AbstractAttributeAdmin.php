<?php

namespace Core\AttributeBundle\Admin;

use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Form\DynamicFormType;
use Core\AttributeBundle\Repository\TypeRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AbstractAttributeAdmin extends Admin
{
    public function getDynamicTypes()
    {
        return $this->getTypeRepository()->fetchDynamicRootTypes();
    }

    protected function addHiddenTypeField(FormMapper $formMapper)
    {
        if (!$this->getSubject()->getId()) {
            $formMapper->add('type_id', 'hidden', array(
                'read_only' => true,
                'mapped' => false,
            ));
            $formMapper->get('type_id')->setData($this->getSubject()->getType()->getId());
        }
    }

    protected function getTypeRepository()
    {
        return $this
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CoreAttributeBundle:Type');
    }

    protected function getContainer()
    {
        return $this->configurationPool->getContainer();
    }


    protected function createFormType()
    {
        $rootType = $this->getSubject()->getType();
        $rootType = new DynamicFormType($this->getContainer()->get('core_attribute.form_type_options_provider.provider_chain'),$rootType);
        return $rootType;
    }

    protected function addTypeFilter(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('type', 'doctrine_orm_choice', array(), 'entity', array(
            'class' => 'Core\AttributeBundle\Entity\Type',
            'query_builder' => function (TypeRepository $repository) {
                return $repository->createDynamicRootTypesQuery();
            },
            'property' => 'label',
        ));
    }

    protected function disableFilterForEntity($filterName, $entity)
    {
        /** @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $filter */
        $filter = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')->getFilters()->getFilter($filterName);
        $filter->disableForEntity($entity);
    }

    protected function initializeTypeFieldFromRequest($object)
    {
        /** @var FormSubmission $object */
        $type = $this->loadTypeFromRequest();
        if ($type) {
            $object->setType($type);
        }
    }

    protected function loadTypeFromRequest()
    {
        if ($this->hasRequest()) {
            if (!($typeId = $this->getRequest()->get('type_id'))) {
                $formData = $this->getRequest()->request->get($this->getUniqid());
                $typeId = empty($formData['type_id']) ? null : $formData['type_id'];
            }

            if ($typeId) {
                $type = $this->getTypeRepository()->find($typeId);
                if (null === $type) {
                    throw new BadRequestHttpException('Type not found: ' . $typeId);
                }
                return $type;
            }
        }
        return null;
    }

    public function getTypeAdmin()
    {
        return $this->configurationPool->getAdminByClass('Core\\AttributeBundle\\Entity\\Type');
    }
}