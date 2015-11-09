<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;


use Core\AttributeBundle\Utils\FormOptionFormTypeResolverInterface;

class ProviderChain
{
    /**
     * @var array|FormOptionFormTypeResolverInterface[]
     */
    private $providers;


    public function __construct(){
        $this->providers = array();
    }

    /**
     * @param ProviderInterface $provider
     * @param string
     */
    public function addProvider(ProviderInterface $provider, $alias){
        $this->providers[$alias] = $provider;
    }

    /**
     * @return array|\Core\AttributeBundle\Utils\FormOptionFormTypeResolverInterface[]
     */
    public function getProviders(){
        return $this->providers;
    }

    /**
     * @param string $alias
     * @throws \RuntimeException
     * @return ProviderInterface
     */
    public function getProvider($alias){
        if(!isset($this->providers[$alias])){
            throw new \RuntimeException(sprintf('No FormTypeOptionsProvider registered with alias: "%s"', $alias));
        }else{
            return $this->providers[$alias];
        }
    }

}