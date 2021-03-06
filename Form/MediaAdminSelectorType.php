<?php


namespace Core\AttributeBundle\Form;


use Core\AdminBundle\Form\AdminFormHelper;
use Core\AttributeBundle\Entity\MediaAttribute;
use Sonata\MediaBundle\Admin\BaseMediaAdmin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaAdminSelectorType extends AbstractType
{
    /** @var BaseMediaAdmin */
    private $mediaAdmin;

    /** @var AdminFormHelper */
    private $adminFormHelper;

    public function __construct(BaseMediaAdmin $mediaAdmin, AdminFormHelper $adminFormHelper)
    {
        $this->mediaAdmin = $mediaAdmin;
        $this->adminFormHelper = $adminFormHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $field = $this->adminFormHelper->createField(
            $this->adminFormHelper->createFormNewMapper($builder),
            'value',
            'sonata_type_model_list',
            array('class' => $this->mediaAdmin->getClass(), 'label' => false,),
            array('class' => $this->mediaAdmin->getClass()),
            \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE,
            false
        );
        $builder->add($field);
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'core_attribute_media_admin_selector_type';
    }

    private function getAdminFormHelper()
    {
        return $this->container->get('sonata.admin.form.helper');
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Core\AttributeBundle\Entity\MediaAttribute',
            'attribute_type'    => null,
        ));
    }


}