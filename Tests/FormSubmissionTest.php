<?php
namespace Core\AttributeBundle\Tests;

use Core\AttributeBundle\Form\AttributeBasedType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Constraints\Collection;

class FormSubmissionTest extends KernelTestCase{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function setUp() {
        self::bootKernel();
        $container = static::$kernel->getContainer();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $this->formFactory = $container->get('form.factory');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testFormCanBeSubmitted(){

        //todo build form instead of querying the database
        $rootType = $this->em->getRepository('CoreAttributeBundle:Type')->find(9);
        if(!$rootType){
            throw new \RuntimeException('The type can not be found');
        }

        $rootFormOptions = $rootType->buildFormOptions();
        $rootForm = $this->formFactory->createBuilder(new AttributeBasedType($rootType), null, $rootFormOptions)->getForm();

        $rootForm->submit(array(
            'alma' => "BARACK",
        ));

    }

}