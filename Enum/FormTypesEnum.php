<?php

namespace Core\AttributeBundle\Enum;

use Symfony\Component\Form\Extension\Validator\Constraints\Form;

class FormTypesEnum{

    const
        FORM = 'form',
        TEXT = 'text',
        TEXT_AREA = 'textarea',
        INTEGER = 'integer',
        CHECKBOX = 'checkbox';

    static function getChoices(){
        return array(
            self::FORM =>  self::FORM,
            self::TEXT =>  self::TEXT,
            self::TEXT_AREA =>  self::TEXT_AREA,
            self::INTEGER =>  self::INTEGER,
            self::CHECKBOX =>  self::CHECKBOX,
        );
    }

}


