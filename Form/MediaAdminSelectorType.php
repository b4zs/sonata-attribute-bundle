<?php


namespace Core\AttributeBundle\Form;


use Core\AdminBundle\Form\AdminFormHelper;
use Core\AttributeBundle\Entity\MediaAttribute;
use Core\AttributeBundle\Form\DataTransformer\AttributeToValueTransformer;
use Core\MediaBundle\Entity\Media;
use Doctrine\Common\Persistence\ObjectManager;
use Core\AdminBundle\Form\DataTransformer\IdToModelTransformer;
use Core\MediaBundle\Admin\ORM\MediaAdmin;
use Sonata\AdminBundle\Form\DataTransformer\ModelToIdTransformer;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\MediaBundle\Admin\BaseMediaAdmin;
use Sonata\MediaBundle\Provider\Pool;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
//        $builder->resetModelTransformers();

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