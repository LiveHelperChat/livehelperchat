<?php

return array(
    'name' => array(
        'type' => 'text',
        'maxlength' => 250,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/contentchunk', 'Name'),
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'identifier' => array(
        'type' => 'text',
        'maxlength' => 50,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/contentchunk', 'Identifier'),
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'content' => array(
        'type' => 'textarea',
        'hidden' => true,
        'ace_editor' => 'json',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/contentchunk', 'Content, checked against JSON format'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'in_active' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/contentchunk', 'In-Active. Do not use in replaceable variables'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 1)
        )),
);
