<?php

namespace Core\AttributeBundle\Admin;

use Core\AttributeBundle\Entity\FormSubmission;
use Core\AttributeBundle\Form\DynamicFormType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\DependencyInjection\Container;

class FormSubmissionAdmin extends Admin
{

    protected $parentAssociationMapping = 'type';

    /** @var Container */
    private $container;

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('create');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('collection.type.label')
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $collectionFormType = $this->createFormType();

        $formMapper->add('collection', $collectionFormType, array(
            'data' => $this->getSubject()->getCollection(),
            'label' => false,
        ));

    }

    /**
     * {@inheritdoc}
     */
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

    private function createFormType()
    {
        $rootType = $this->getSubject()->getType();
        $rootType = new DynamicFormType($rootType);
        return $rootType;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit': return 'CoreAttributeBundle:FormSubmissionAdmin:edit.html.twig';
            default:     return parent::getTemplate($name);
        }
    }

}
