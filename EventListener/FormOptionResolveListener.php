<?php

namespace Core\AttributeBundle\EventListener;

use Core\AttributeBundle\Event\FormOptionResolveEvent;
use Core\AttributeBundle\Utils\FormOptionFormTypeResolver;

class FormOptionResolveListener
{

    public function resolve(FormOptionResolveEvent $event){
        $resolver = new FormOptionFormTypeResolver();
        try{
            $resolvedOptions = $resolver->resolve($event->getOption(),$event->getFormType());
        }catch (\RuntimeException $e){
            $resolvedOptions = false;
        }

        if($resolvedOptions !== false){
            $event->setResult($resolvedOptions);
            $event->stopPropagation();
        }
    }

    public function resolveAttr(FormOptionResolveEvent $event){
        $resolver = new FormOptionFormTypeResolver();
        try{
            $resolvedOptions = $resolver->resolveAttr($event->getOption(),$event->getFormType());
        }catch (\RuntimeException $e){
            $resolvedOptions = false;
        }

        if($resolvedOptions !== false){
            $event->setResult($resolvedOptions);
            $event->stopPropagation();
        }
    }

}