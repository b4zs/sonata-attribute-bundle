<?php

namespace Core\AttributeBundle\Admin;

use Core\AttributeBundle\Entity\FormSubmission;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DynamicFormType;
use Core\AttributeBundle\Utils\TypeHelper;
use Doctrine\Common\Util\Debug;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
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
        $collection->remove('create');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    /**
     * @param $object
     */
    public function preUpdate($object)
    {
        /** @var FormSubmission $object */
        $object->setUpdatedAt(new \DateTime());
        parent::preUpdate($object);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->disableFilterForEntity('soft_deleteable','Core\AttributeBundle\Entity\Type');

        $listMapper
            ->add('collection.type.label')
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

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->disableFilterForEntity('soft_deleteable','Core\AttributeBundle\Entity\Type');
        $collectionFormType = $this->createFormType();

        $formMapper->add('collection', $collectionFormType, array(
            'data' => $this->getSubject()->getCollection(),
            'label' => false,
        ));

    }

    /**
     * {@inheritdoc}
     */
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
        $rootType = new DynamicFormType($this->container->get('core_attribute.form_type_options_provider.provider_chain'),$rootType);
        return $rootType;
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

    protected function disableFilterForEntity($filterName, $entity)
    {
        /** @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $filter */
        $filter = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')->getFilters()->getFilter($filterName);
        $filter->disableForEntity($entity);
    }

}
