<?php

return array(
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

            return $items;
        },
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 2)
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

