<?php

return array(
    'name' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Name for personal purposes'),
        'required' => true,
        'link' => erLhcoreClassDesign::baseurl('form/collected'),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'content' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Content'),
        'required' => false,
        'ace_editor' => 'html',
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'name_attr' => array (
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Name attributes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'intro_attr' => array (
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Introduction attributes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'xls_columns' => array (
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','XLS Columns'),
        'required' => false,
        'height'	=> '100px',
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'recipient' => array (
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Recipient'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'post_content' => array(
        'type' => 'textarea',
        'ace_editor' => 'html',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Post content after form is submitted'),
        'required' => false,
        'hidden' => true,
        'height'	=> '150px',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'pagelayout' => array (
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Custom pagelayout'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'hide_content_on_success' => array(
        'type' => 'checkbox',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Do not show default content on success form submit.'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'hide_form_name' => array(
        'type' => 'checkbox',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide name on filling page.'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'one_fillment_per_chat' => array(
        'type' => 'checkbox',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','One fillment per chat. Previous filled form data will be reused.'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'active' => array (
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Active'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'form_type' => array (
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Internal form (only operators can fill it)'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ))
);

?>