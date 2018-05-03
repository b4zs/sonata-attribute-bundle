<?php


namespace Core\AttributeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ColorInputType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'color';
    }


    public function getName()
    {
        return 'color_input';
    }

    public function getParent()
    {
        return TextType::class;
    }


}