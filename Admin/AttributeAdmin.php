<?php


namespace Core\AttributeBundle\Admin;


use Core\AttributeBundle\Entity\Attribute;
use Core\AttributeBundle\Entity\CollectionAttribute;
use Core\AttributeBundle\Entity\Type;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AttributeAdmin extends AbstractAttributeAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->disableFilterForEntity('soft_deleteable','Core\AttributeBundle\Entity\Type');
        $collectionFormType = $this->createFormType();

        $formMapper->with('Submission');
        $formMapper->add('data', $collectionFormType, array(
            'data' => $this->getSubject(),
            'label' => false,
            'inherit_data' => true,
            ),array(
                'type' => $collectionFormType->getName(),
            )
        );
        $this->addHiddenTypeField($formMapper);
        $formMapper->end();
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id');
        $list->addIdentifier('type', null, array(
            'template' => 'CoreAttributeBundle:AttributeAdmin:list_field_type.html.twig',
        ));
        $list->add('value', null, array(
            'template' => 'CoreAttributeBundle:AttributeAdmin:list_field_value.html.twig',
        ));
        $list->add('_action', 'actions', array(
            'actions' => array(
                'edit' => array(),
                'delete' => array(),
            )
        ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $this->addTypeFilter($datagridMapper);
        $datagridMapper->add('show_descendants', 'doctrine_orm_callback', array(
            'callback' => function($queryBuilder, $alias, $field, $value) {
                $ra = $queryBuilder->getRootAliases();
                $ra = reset($ra);

                if ($value && $value['value']) { // show all, no filter
                    //do nothing
                } else { //hide non-collection values
                    $queryBuilder->andWhere($ra.'.parent IS NULL');
                }
            },
            'label' => 'Show non-root values',
        ), 'checkbox', array(
        ));
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        
        $query->innerJoin('o.type', 'record_type');
        $query->andWhere('record_type.deletedAt IS NULL');

        $em = $this->configurationPool->getContainer()->get('doctrine.orm.default_entity_manager');
        if ($em->getFilters()->has('soft_deleteable')) {
            $em->getFilters()->disable('soft_deleteable');
        }


        return $query;
    }

    public function isGranted($name, $object = null)
    {
        if ('EDIT' === $name && $object instanceof Attribute && $object->getId() && !$object instanceof CollectionAttribute) {
            return false;
        } else {
            return parent::isGranted($name, $object);
        }
    }

    public function getNewInstance()
    {
        $type = $this->loadTypeFromRequest();
        if ($type instanceof Type) {
            $className = $type->getAttributeClass();
            /** @var Attribute $object */
            $object = new $className;
            $object->setType($type);

            return $object;
        } else {
            return new CollectionAttribute();
        }
    }

    public function toString($object)
    {
        return $object->getId() ? sprintf('Attribute#%d', $object->getId()) : '+';
    }


}