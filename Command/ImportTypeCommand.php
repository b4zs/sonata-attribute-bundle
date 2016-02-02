<?php

namespace Core\AttributeBundle\Command;


use Core\AttributeBundle\Entity\Type;
use Core\AttributeBundle\Factory\TypeFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Yaml\Yaml;

class ImportTypeCommand extends ContainerAwareCommand
{

    private static $requiredFields = array('preset_alias', 'name', 'label', 'position', 'options');

    /** @var TypeFactory */
    private $typeFactory;

    /** @var EntityManager */
    private $entityManager;

    protected function configure()
    {
        $this
            ->setName('attribute:import-type')
            ->addArgument('path', InputArgument::OPTIONAL, 'Import path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->typeFactory      = $this->getContainer()->get('core_attribute.factory.type');
        $this->entityManager    = $this->getContainer()->get('doctrine.orm.entity_manager');

        $paths = $this->loadConfigFiles($input);

        $i = 0;
        foreach($paths as $path){

            $output->writeln(sprintf('<comment>Loading %s ...</comment>', $path));

            $type = $this->processConfig($output, $path);

            $output->writeln(sprintf('<info>Persisting %s</info>', $type->getName()));

            if(20 == $i){
                $this->persistType($type, true);
                $i = 0;
            }else{
                $this->persistType($type, false);
            }

            $i++;
        }

        $this->entityManager->flush();

    }

    private function groupByParent(Type $type){

        $groupedTypes = array();

        if($type->getChildren()->count()){
            foreach($type->getChildren() as $existingChild){
                $groupedTypes[$existingChild->getParent()->getName()][] = $existingChild;
                $groupedTypes += $this->groupByParent($existingChild);
            }
        }

        return $groupedTypes;

    }

    private function update(Type $existingType, $groupedTypes)
    {

        if(array_key_exists($existingType->getName(), $groupedTypes)){

            foreach($groupedTypes[$existingType->getName()] as $type){

                $isExist = $existingType->getChildren()->exists(function($key, $element) use ($type){
                    return $element->getName() === $type->getName();
                });

                if(!$isExist){
                    $existingType->addChildren($type);
                }

                foreach($existingType->getChildren() as $child){
                    $this->update($child, $groupedTypes);
                }

            }
        }

        return $existingType;
    }

    private function deserialize(array $type)
    {
        $typeObj = $this->arrayToType($type);

        if($type['__children']){
            foreach($type['__children'] as $child){
                $typeObj->addChildren($this->deserialize($child));
            }
        }

        return $typeObj;
    }

    protected function loadYaml($path)
    {
        $yaml = Yaml::parse($path);
        return $yaml;
    }

    protected function persistType($type, $flush = false)
    {
        $this->entityManager->persist($type);

        if($flush){
            $this->entityManager->flush();
        }
    }

    /**
     * @param array $type
     * @return Type
     */
    private function arrayToType(array $type)
    {
        $this->validateTypeArray($type);

        $typeObj = $this->typeFactory->create($type['preset_alias']);
        $typeObj->setName($type['name']);
        $typeObj->setLabel($type['label']);
        $typeObj->setPosition($type['position']);
        $typeObj->setFormOptions($type['options']);
        return $typeObj;
    }

    /**
     * @param array $type
     * @return bool
     */
    private function validateTypeArray(array $type)
    {
        if($missingFields = array_diff(self::$requiredFields, array_keys($type))){
            throw new \InvalidArgumentException(sprintf('All the form type definition must contain the following keys: %s, missing: %s', json_encode(self::$requiredFields), json_encode(array_values($missingFields))));
        }
    }

    /**
     * @param Type $type
     * @return null|Type
     */
    protected function getExistingType(Type $type)
    {
        $typeRepository = $this->entityManager->getRepository('CoreAttributeBundle:Type');
        $existingType = $typeRepository->createQueryBuilder('type')
            ->andWhere('type.parent IS NULL')
            ->andWhere('type.name = :type_name')
            ->setParameter('type_name', $type->getName())
            ->getQuery()
            ->getOneOrNullResult();

        return $existingType;
    }

    protected function loadConfigFiles(InputInterface $input)
    {
        $path = $input->getArgument('path');
        $paths = array();


        if(null !== $path){
            if (!file_exists($path)) {
                throw new \InvalidArgumentException(sprintf('Fle not exists: %s', $path));
            }

            $paths[] = $path;
        }else{
            $kernel = $this->getContainer()->get('kernel');
            $finder = new Finder();

            $bundles = $kernel->getBundles();

            /** @var Bundle $bundle */
            foreach($bundles as $bundle){
                $path = sprintf('%s/Resources/config/form', $bundle->getPath());
                if(is_dir($path)){
                    $finder->files()->name('*.yml')->in($path);
                    foreach ($finder as $file) {
                        $paths[] = $file->getRealpath();
                    }
                }
            }
        }

        return $paths;
    }


    protected function processConfig(OutputInterface $output, $path)
    {
        $typeArray = $this->loadYaml($path);
        $type = $this->deserialize($typeArray);

        if ($existingType = $this->getExistingType($type)) {
            //update
            $groupedTypes = $this->groupByParent($type);
            $type = $this->update($existingType, $groupedTypes);
        } else {
            //new
            //do nothing just save it
        }

        return $type;
    }

}