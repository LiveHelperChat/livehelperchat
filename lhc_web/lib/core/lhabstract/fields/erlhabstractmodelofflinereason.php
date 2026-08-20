<?php

return array(
    'id' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/product','ID'),
        'required' => false,
        'width' => 1,
        'hide_edit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'name' => array(
        'type' => 'text',
        'maxlength' => 250,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Name'),
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'description' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Description'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'icon' => array(
        'type' => 'text',
        'maxlength' => 250,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Icon'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Position'),
        'required' => false,
        'default' => 0,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
);
