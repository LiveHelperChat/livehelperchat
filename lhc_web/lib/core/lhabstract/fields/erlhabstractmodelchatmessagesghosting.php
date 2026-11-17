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
    'type_icon' => array(
        'type' => 'text',
        'trans' => '',
        'required' => false,
        'width' => 1,
        'no_wrap' => true,
        'frontend' => 'material_icon',
        'wrap_start' => '<span class="material-icons align-middle text-muted" aria-hidden="true">',
        'wrap_end' => '</span>',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'name' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Name for personal reference'),
        'required' => true,
        'maxlength' => 100, 
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Enabled'),
        'required' => false,
        'hide_optional' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'rule_type' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Applies to'),
        'required' => true,
        'hide_optional' => true,
        'name_attr' => 'name',
        'frontend' => 'name',
        'hidden' => true,
        'source' => function () {

            $items = [];
            $item = new stdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_VISITOR_TO_OPERATOR;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Messages from visitors to agents');
            $items[] = $item;

            $item = new stdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_OPERATOR_TO_VISITOR;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Messages from agents to visitors');
            $items[] = $item;

            $item = new stdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_REST;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Applies to rest API calls');
            $items[] = $item;

            $item = new stdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_VISITOR_EMAIL;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'E-mail messages from visitors');
            $items[] = $item;

            $item = new stdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_OPERATOR_EMAIL;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'E-mail messages from agents');
            $items[] = $item;

            return $items;
        },
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 4)
        )),
    'pattern' => array(
        'type' => 'textarea',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Place you replacement pattern here.'),
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Guardrails rules'),
        'required' => false,
        'hidden'   => true,
        'maxlength' => 255,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'dep_ids' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Stores JSON array of department IDs to which the rule applies. Empty means all departments.'),
        'required' => false,
        'hidden'   => true,
        'maxlength' => 255,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'v_warning' => array(
        'hidden'   => true,
        'type' => 'textarea',
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to visitor after posting sensitive information'),
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Auto reply warning if visitor is posting information to agent which does not have permission to view sensitive data.'),
        'required' => false,
        'maxlength' => 255,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    )
);

