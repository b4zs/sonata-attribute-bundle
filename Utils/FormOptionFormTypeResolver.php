<?php

namespace Core\AttributeBundle\Utils;

use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class FormOptionFormTypeResolver implements FormOptionFormTypeResolverInterface{

    public function resolve($option, $formType){

        $builderParameters = array('child' => $option, 'type' => null, 'options' => array('required' => false));

        switch($option):
            case 'success_flash':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'options' => array('sonata_help' => 'Placeholders: <br>[current_datetime] = Date and time of submission',)
                ));
            case 'value_class':
            case 'attribute_class':
            case 'data_class':
            case 'label':
            case 'empty_data':
            case 'default_protocol':
            case 'placeholder':
            case 'empty_value':
            case 'context':
            case 'provider':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'text',
                ));
                break;
            case 'required':
            case 'disabled':
            case 'trim':
            case 'expanded':
            case 'multiple':
            case 'with_minutes':
            case 'with_seconds':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'checkbox',
                ));
                break;
            case 'scale':
            case 'precision':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'integer',
                ));
                break;
            case 'rounding_mode':

                $choices = array();
                if($formType == 'integer'){
                    $choices = array(
                        IntegerToLocalizedStringTransformer::ROUND_DOWN => 'Round down',
                        IntegerToLocalizedStringTransformer::ROUND_UP => 'Round up',
                        IntegerToLocalizedStringTransformer::ROUND_CEILING => 'Round ceiling',
                        IntegerToLocalizedStringTransformer::ROUND_FLOOR => 'Round floor',
                        IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN => 'Round half down',
                        IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN => 'Round half even',
                        IntegerToLocalizedStringTransformer::ROUND_HALF_UP => 'Round half up',
                    );
                }elseif($formType == 'number'){
                    $choices = array(
                        NumberToLocalizedStringTransformer::ROUND_DOWN => 'Round down',
                        NumberToLocalizedStringTransformer::ROUND_UP => 'Round up',
                        NumberToLocalizedStringTransformer::ROUND_CEILING => 'Round ceiling',
                        NumberToLocalizedStringTransformer::ROUND_FLOOR => 'Round floor',
                        NumberToLocalizedStringTransformer::ROUND_HALF_DOWN => 'Round half down',
                        NumberToLocalizedStringTransformer::ROUND_HALF_EVEN => 'Round half even',
                        NumberToLocalizedStringTransformer::ROUND_HALF_UP => 'Round half up',
                    );
                }

                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'choice',
                    'options' => array(
                        'choices' => $choices,
                    )
                ));
                break;
            case 'widget':
            case 'date_widget':
            case 'time_widget':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'choice',
                    'options' => array(
                        'choices' => array(
                            'choice' => 'Choice',
                            'text' => 'Text',
                            'single_text' => 'Single text'
                        ),
                    ),
                ));
                break;
            case 'format':
            case 'date_format':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'choice',
                    'options' => array(
                        'choices' => array(
                            IntlDateFormatter::NONE => 'None',
                            IntlDateFormatter::SHORT => 'Short',
                            IntlDateFormatter::MEDIUM => 'Medium',
                            IntlDateFormatter::LONG => 'Long',
                            IntlDateFormatter::FULL => 'Full',
                            IntlDateFormatter::GREGORIAN => 'Georgian',
                            IntlDateFormatter::TRADITIONAL => 'Traditional',
                        ),
                    ),
                ));
                break;
            case 'currency':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'currency',
                ));
                break;
            case 'attr':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'form',
                ));
                break;
            case 'choices':
            case 'preferred_choices':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'yaml_array',
                ));
                break;
            default:
                throw new \RuntimeException(sprintf('The option "%s" can not be resolved.', $option));
                break;
        endswitch;

        return array_values($builderParameters);
    }

    public function resolveAttr($option, $formType){

        $builderParameters = array('child' => $option, 'type' => null, 'options' => array('required' => false));

        switch($option):
            case 'readonly':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'checkbox',
                ));
                break;
            case 'maxlength':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'integer',
                ));
                break;
            case 'class':
            case 'style':
            case 'placeholder':
                $builderParameters = array_replace_recursive($builderParameters, array(
                    'type' => 'text',
                ));
                break;
            default:
                throw new \RuntimeException(sprintf('The option "%s" can not be resolved.', $option));
                break;
        endswitch;

        return array_values($builderParameters);
    }

}
