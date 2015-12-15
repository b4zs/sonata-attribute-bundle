<?php

use Symfony\Component\Config\Loader\LoaderInterface;

class TestKernel extends Symfony\Component\HttpKernel\Kernel
{
	public function registerBundles()
	{
		$return = array(
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Sonata\MediaBundle\SonataMediaBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
//			new Symfony\Bundle\MonologBundle\MonologBundle(),
//			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new JMS\SerializerBundle\JMSSerializerBundle($this),
//			new FOS\RestBundle\FOSRestBundle(),
		);
		$return[] = new Core\AttributeBundle\CoreAttributeBundle();

		return $return;
	}


	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		$loader->load(__DIR__.'/config.yml');
	}
};
