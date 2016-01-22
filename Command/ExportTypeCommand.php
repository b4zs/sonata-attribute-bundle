<?php

namespace Core\AttributeBundle\Command;


use Core\AttributeBundle\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ExportTypeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('attribute:export-type')
            ->addArgument('path', InputArgument::OPTIONAL, 'Export path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exportPath = $this->getExportPath($input);
        $rootTypes = $this->getRootTypes();
        $fs = new Filesystem();

        foreach($rootTypes as $type){
            $filename = sprintf('%s/%s.yml', $exportPath, $type->getName());

            $output->writeln(sprintf('<comment>Writing %s ...</comment>', $filename));

            $serializedType = $this->serialize($type);
            $yaml = Yaml::dump($serializedType, 99, 2);
            $fs->dumpFile($filename, $yaml);

            $output->writeln(sprintf('<info>Done writing %s</info>', $filename));
        }

    }

    private function serialize(Type $type)
    {
        $typeArray = $this->typeToArray($type);

        if($type->getChildren()->count()){

            /** @var Type $child */
            foreach($type->getChildren() as $child){
                $typeArray['__children'][] = $this->serialize($child);
            }

        }

        return $typeArray;
    }

    private function typeToArray(Type $type){

        $formOptions = $type->getFormOptions();
        unset($formOptions['attribute_class']);
        unset($formOptions['data_class']);

        return array(
            'name' => $type->getName(),
            'preset_alias' => $type->getFormType(),
            'label' => $type->getLabel(),
            'position' => $type->getPosition(),
            'options' => $formOptions,
            '__children' => array(),
        );
    }

    /**
     * @param InputInterface $input
     * @return mixed|string
     */
    protected function getExportPath(InputInterface $input)
    {
        if (!$exportPath = $input->getArgument('path')) {
            $exportPath = sprintf('%s/export/form_type/', $this->getContainer()->getParameter('kernel.cache_dir'));
        }
        $exportPath = rtrim($exportPath, '/');
        return $exportPath;
    }

    /**
     * @return mixed
     */
    protected function getRootTypes()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('CoreAttributeBundle:Type');
        $queryBuilder = $repository->createQueryBuilder('type');

        $rootTypes = $queryBuilder
            ->andWhere('type.parent IS NULL')
            ->getQuery()
            ->execute();
        return $rootTypes;
    }

}