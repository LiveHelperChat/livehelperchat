<?php

return array(
    'copy_action' => array(
        'type' => 'action',
        'link' => 'audit/logrecord',
        'is_modal' => true,
        'link_class' => 'material-icons',
        'link_title' => 'info_outline',
        'is_iframe' => false,
        'width' => '1%',
        'trans' => '',
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'object_id' => array(
        'type' => 'text_display',
        'frontend' => 'id_frontend',
        'no_wrap' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', '[Record ID] [Object ID]'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'category' => array(
        'type' => 'text_display',
        'placeholder' => '',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Category'),
        'required' => false,
        'no_wrap' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'message' => array(
        'type' => 'text_pre',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message'),
        'required' => false,
        'width' => '80',
        'wrap_start' => '<textarea class="form-control fs11" rows="10">',
        'wrap_end' => '</textarea>',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'file' => array(
        'type' => 'text_display',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'File'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'If same identifier used for two columns, both values will be represented in single column.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'line' => array(
        'type' => 'text_display',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Line'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'severity' => array(
        'type' => 'text_display',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Severity'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'source' => array(
        'type' => 'text_display',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Source'),
        'required' => false,
        'no_wrap' => true,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'time' => array(
        'type' => 'text_display',
        'no_wrap' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Time'),
        'required' => false,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    )
);

