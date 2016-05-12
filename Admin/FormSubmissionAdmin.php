<?php

namespace Core\AttributeBundle\Admin;

use Core\AttributeBundle\Entity\FormSubmission;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DynamicFormType;
use Core\AttributeBundle\Repository\TypeRepository;
use Core\AttributeBundle\Utils\TypeHelper;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FormSubmissionAdmin extends AbstractAttributeAdmin
{
    protected $parentAssociationMapping = 'type';

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addTypeFilter($datagridMapper);
    }

    public function preUpdate($object)
    {
        /** @var FormSubmission $object */
        $object->setUpdatedAt(new \DateTime());
        parent::preUpdate($object);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $this->disableFilterForEntity('soft_deleteable','Core\AttributeBundle\Entity\Type');

        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('type', null, array(
                'template' => 'CoreAttributeBundle:AttributeAdmin:list_field_type.html.twig',
            ))
            ->add('collection', null, array(
                'template' => 'CoreAttributeBundle:FormSubmissionAdmin:list_collection_value_field.html.twig',
                'label' => 'label.form_data',
            ))
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->disableFilterForEntity('soft_deleteable','Core\AttributeBundle\Entity\Type');
        $collectionFormType = $this->createFormType();

        $formMapper->with('Submission');
            $formMapper->add('collection', $collectionFormType, array(
                'data' => $this->getSubject()->getCollection(),
                'label' => false,
            ));
        $this->addHiddenTypeField($formMapper);
        $formMapper->end();
    }

    protected function configureShowFields(ShowMapper $filter)
    {
        $this->disableFilterForEntity('soft_deleteable','Core\AttributeBundle\Entity\Type');
        $filter
            ->add('collection.type.label')
            ->add('createdAt')
            ->add('collection', null, array(
                'label' => 'label.form_data',
            ));
    }

    public function toString($object)
    {
        if($object instanceof FormSubmission && $object->getId()){
            return "Form Submission - #".$object->getId();
        }

        return "New";
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        return $query;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit': return 'CoreAttributeBundle:FormSubmissionAdmin:edit.html.twig';
            case 'show': return 'CoreAttributeBundle:FormSubmissionAdmin:show.html.twig';
            default:     return parent::getTemplate($name);
        }
    }

    public function getExportFields() {

        $type = $this->getParent()->getSubject();

        $paths = TypeHelper::flattenType($type);

        $paths = array_map(function($n){
            return str_replace(current(explode('.', $n)), 'collection', $n).'.value';
        },$paths);

        return array_merge(array('id', 'createdAt'),$paths);
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance();
        $this->initializeTypeFieldFromRequest($object);
        return $object;
    }
}
