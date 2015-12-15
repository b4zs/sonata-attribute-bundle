<?php

// get the autoload file
$dir = __DIR__;
$lastDir = null;
while ($dir !== $lastDir) {
	$lastDir = $dir;
	if (file_exists($dir.'/vendor/autoload.php')) {
		require_once $dir.'/vendor/autoload.php';
		break;
	}
	$dir = dirname($dir);
}

class TestKernel extends Symfony\Component\HttpKernel\Kernel
{
	/** @var string */
	private $testCase;

	public function __construct($environment, $debug, $testCase)
	{
		parent::__construct($environment, $debug);
		$this->testCase = $testCase;
	}


	public function registerBundles()
	{
		return array(
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Sonata\MediaBundle\SonataMediaBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new JMS\SerializerBundle\JMSSerializerBundle($this),
			new Core\AttributeBundle\CoreAttributeBundle(),
		);
	}

	public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
	{
		$loader->load(__DIR__.'/config.yml');
	}

	public function getRootDir()
	{
		return sys_get_temp_dir().self::VERSION.'/'.$this->testCase;
	}
}
