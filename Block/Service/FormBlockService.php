<?php


namespace Core\AttributeBundle\Block\Service;


use Core\AttributeBundle\Entity\FormSubmission;
use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Form\DynamicFormType;
use Core\BlockBundle\Block\Service\BaseTransformedSettingsBlockService;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormBlockService extends BaseTransformedSettingsBlockService
{

	const SERVICE_ID = 'core_attribute.block.service.form';

	/** @var  ContainerInterface */
	protected $container;

	public function buildEditForm(FormMapper $form, BlockInterface $block)
	{
		$form->add('settings', 'sonata_type_immutable_array', array(
			'label' => false,
			'keys' => array(
				array('title', 'text', array('required' => true)),
				array('user_form', 'entity', array(
					'class'     => 'Core\AttributeBundle\Entity\Type',
					'required'  => true,
					'property'	=> 'label',
					'query_builder' => function($repository){
						return $repository->createQueryBuilder('type')
							->where('type.formType = :formType')
							->setParameter('formType', 'form');
					},
				)),
				array('template', 'choice', array(
					'required'  => true,
					'choices'   => array('CoreAttributeBundle:Block:form_base.html.twig' => 'defult') + $this->getTemplateChoices(),
				)),
			),
		));
	}

	public function setDefaultSettings(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'template'      => 'CoreAttributeBundle:Block:form_base.html.twig',
			'title'			=> '',
			'user_form'		=> null,
		));
	}

	public function execute(BlockContextInterface $blockContext, Response $response = null)
	{
		$messages = array();

		$form = $this->createForm($blockContext->getBlock());

		$request = $this->getRequest();
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$entityManager = $this->getEntityManager();

				$formData = $form->getData();
				$submission = $this->createSubmission($blockContext->getBlock()->getSetting('user_form'), $formData);

				$entityManager->persist($submission);
				$entityManager->flush();

				$messages[] = array(
					'type'      => 'success',
					'message'   => $this->getTranslator()->trans('message.form_submitted')
				);

				$form = $this->createForm($blockContext->getBlock());
			}
		}

		return $this->createResponse($blockContext, $response, $form, $messages);
	}

	private function getTemplateChoices()
	{
		$site = $this->container->get('sonata.page.site.selector')->retrieve();
		$templateHelper = $this->container->get('core.page.theming.theme_helper');
		$choices = $templateHelper->getBlockTemplateChoicesOnSite(self::SERVICE_ID, $site);

		return $choices;
	}

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer($container)
	{
		$this->container = $container;
	}

	protected function transformSettings(array $settings)
	{
		if($settings['user_form'] instanceof Type){
			$settings['user_form'] = $settings['user_form']->getId();
		}

		return $settings;
	}

	protected function reverseTransformSettings(array $settings)
	{
		if(!is_int($settings['user_form'])){
			throw new \RuntimeException('Form id must be a valid integer');
		}

		$settings['user_form'] = $this->container->get('doctrine.orm.entity_manager')
			->getRepository('CoreAttributeBundle:Type')->find($settings['user_form']);

		return $settings;
	}

	protected function getRequest()
	{
		return $this->container->get('request_stack')->getCurrentRequest();
	}

	protected function getEntityManager()
	{
		return $this->container->get('doctrine.orm.default_entity_manager');
	}

	protected function getTranslator()
	{
		return $this->container->get('translator');
	}

	protected function getRouter()
	{
		return $this->container->get('router');
	}

	protected function getProviderChain(){
		return $this->container->get('core_attribute.form_type_options_provider.provider_chain');
	}

	/**
	 * @param BlockInterface $block
	 * @return \Symfony\Component\Form\Form
	 */
	protected function createForm(BlockInterface $block)
	{
		$formFactory = $this->container->get('form.factory');
		/** @var Type $rootType */
		$rootType = $block->getSetting('user_form');

		if(!$rootType instanceof Type /*|| $settings['user_form']->getFormType() !== 'form'*/){
			throw new \RuntimeException('Form can not be found');
		}

		$page = $this->container->get('sonata.page.cms_manager_selector')->retrieve()->getCurrentPage();
		$url = $page && $block
			? $this->getRouter()->generate('sonata_page_ajax_block', array(
				'pageId' => $page->getId(),
				'blockId' => $block->getId(),
			))
			: '#';

		$rootFormOptions = $rootType->getFormOptions();
		$rootFormOptions = array_merge(
			array(
				'action'            => $url,
				'method'            => 'POST',
			),
			$rootFormOptions);

		$rootForm = $formFactory->createBuilder(new DynamicFormType($this->getProviderChain(), $rootType), null, $rootFormOptions)->getForm();
		return $rootForm;
	}

	/**
	 * @param $type
	 * @param $formData
	 * @return FormSubmission
	 */
	protected function createSubmission($type, $formData)
	{
		$submission = new FormSubmission();
		$submission->setType($type);
		$submission->setCollection($formData);
		return $submission;
	}

	/**
	 * @param BlockContextInterface $blockContext
	 * @param Response $response
	 * @param $form
	 * @param $messages
	 * @return Response
	 */
	protected function createResponse(BlockContextInterface $blockContext, Response $response, $form, $messages)
	{
		return $this->renderResponse(
			$blockContext->getBlock()->getSetting('template'),
			array(
				'block_context' => $blockContext,
				'block' => $blockContext->getBlock(),
				'form' => $form->createView(),
				'messages' => $messages,
			),
			$response
		);
	}
} 