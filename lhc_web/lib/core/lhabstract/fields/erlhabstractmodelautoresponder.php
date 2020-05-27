<?php

return array(
    'siteaccess' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Language, leave empty for all. E.g lit, rus, ger etc...'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'name' => array(
        'type' => 'text',
        'maxlength' => 50,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Name'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'operator' => array(
        'type' => 'text',
        'maxlength' => 50,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Operator. Visitor will see this operator nick.'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'position' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Position'),
        'required' => true,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'nreply_bot_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'nreply_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['nreply_bot_id']) ? $this->bot_configuration_array['nreply_bot_id'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'onhold_bot_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'onhold_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['onhold_bot_id']) ? $this->bot_configuration_array['onhold_bot_id'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'mint_reset' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Minimum time in second how long sync has to be stopped before allowing reset auto responder'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        )),
    'maxt_reset' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Maximum time in seconds how long sync has to be stopped before we do not reset auto responder'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        )),
    'dreset_survey' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Disable reset auto responder if visitor was redirected to survey'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'dep_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Department'),
        'required' => false,
        'frontend' => 'dep_frontend',
        'source' => 'erLhcoreClassModelDepartament::getList',
        'hide_optional' => !empty($departmentFilterdefault = erLhcoreClassUserDep::conditionalDepartmentFilter()),
        'params_call' => $departmentFilterdefault,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        )),
     'user_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'User'),
        'required' => false,
        'frontend' => 'user',
        'source' => 'erLhcoreClassModelDepartament::getList',
        'hide_optional' => false,
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        )),
    'wait_message' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Wait message. Visible when users starts chat and is waiting for someone to accept a chat.'),
        'required' => false,
        'hidden' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'wait_timeout' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Wait timeout.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

    'timeout_message_2' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show visitor this message when wait timeout passes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'wait_timeout_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Wait timeout.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_message_3' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show visitor this message when wait timeout passes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'wait_timeout_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Wait timeout.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_message_4' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show visitor this message when wait timeout passes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'wait_timeout_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Wait timeout.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_message_5' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show visitor this message when wait timeout passes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'wait_timeout_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Wait timeout.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_message' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show visitor this message when wait timeout passes'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'repeat_number' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'How many times repeat message? Applied only to first message.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        )),
    'survey_timeout' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Redirect visitor to survey if visitor does not responds within N seconds'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'survey_id' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Survey'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_hold_1' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_hold_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_hold_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_hold_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_hold_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_reply_1' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_hold' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Default on hold message'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_hold_message_1' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [1]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_hold_message_2' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [2]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_hold_message_3' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [3]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_hold_message_4' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [4]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'timeout_hold_message_5' => array(
        'type' => 'textarea',
        'height' => '86px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [5]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

       'timeout_reply_message_1' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [1]'),
            'required' => false,
            'hidden' => true,
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_reply_message_2' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [2]'),
            'required' => false,
            'hidden' => true,
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_reply_message_3' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [3]'),
            'required' => false,
            'hidden' => true,
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_reply_message_4' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [4]'),
            'required' => false,
            'hidden' => true,
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_reply_message_5' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [5]'),
            'required' => false,
            'hidden' => true,
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
        'timeout_op_reply_message_1' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [1]'),
            'required' => false,
            'hidden' => true,
            'main_attr' => 'bot_configuration_array',
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_op_reply_message_2' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [2]'),
            'required' => false,
            'hidden' => true,
            'main_attr' => 'bot_configuration_array',
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_op_reply_message_3' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [3]'),
            'required' => false,
            'hidden' => true,
            'main_attr' => 'bot_configuration_array',
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_op_reply_message_4' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [4]'),
            'required' => false,
            'hidden' => true,
            'main_attr' => 'bot_configuration_array',
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
       'timeout_op_reply_message_5' => array(
            'type' => 'textarea',
            'height' => '86px',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message for timeout [5]'),
            'required' => false,
            'hidden' => true,
            'main_attr' => 'bot_configuration_array',
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

        'wait_op_timeout_reply_1' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [1]'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'wait_op_timeout_reply_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [2]'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'wait_op_timeout_reply_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [3]'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'wait_op_timeout_reply_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [4]'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'wait_op_timeout_reply_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [5]'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'close_message' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to visitor on chat close'),
        'height' => '86px',
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', array()
        )),
        'offline_message' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to visitor if department is offline'),
        'height' => '86px',
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', array()
        )),
        'multilanguage_message' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to visitor if operator speaks same language as visitor.'),
        'height' => '86px',
        'required' => false,
        'hidden' => true,
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', array()
        )),
        'nreply_op_bot_id_1' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'nreply_op_1_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['nreply_op_bot_id_1']) ? $this->bot_configuration_array['nreply_op_bot_id_1'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

        'nreply_op_bot_id_2' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'nreply_op_2_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['nreply_op_bot_id_2']) ? $this->bot_configuration_array['nreply_op_bot_id_2'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

        'nreply_op_bot_id_3' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'nreply_op_3_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['nreply_op_bot_id_3']) ? $this->bot_configuration_array['nreply_op_bot_id_3'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),


        'nreply_op_bot_id_4' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'nreply_op_4_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['nreply_op_bot_id_4']) ? $this->bot_configuration_array['nreply_op_bot_id_4'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

        'nreply_op_bot_id_5' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
        'nreply_op_5_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['nreply_op_bot_id_5']) ? $this->bot_configuration_array['nreply_op_bot_id_5'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),





    'wait_timeout_reply_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [2]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_reply_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [3]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_reply_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [4]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'wait_timeout_reply_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Timeout. [5]'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'ignore_pa_chat' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Do not send messages to pending chat if chat is assigned to operator.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'only_proactive' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'This auto responder applies only to proactive invitations.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'copy_action' => array(
        'type' => 'action',
        'link' => 'abstract/copyautoresponder',
        'is_modal' => true,
        'link_class' => 'btn btn-secondary btn-xs',
        'link_title' => 'Copy',
        'width' => '1%',
        'trans' => '',
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),


    'pending_op_bot_id_1' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'pending_op_1_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['pending_op_bot_id_1']) ? $this->bot_configuration_array['pending_op_bot_id_1'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

    'pending_op_bot_id_2' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'pending_op_2_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['pending_op_bot_id_2']) ? $this->bot_configuration_array['pending_op_bot_id_2'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

    'pending_op_bot_id_3' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'pending_op_3_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['pending_op_bot_id_3']) ? $this->bot_configuration_array['pending_op_bot_id_3'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

    'pending_op_bot_id_4' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'pending_op_4_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['pending_op_bot_id_4']) ? $this->bot_configuration_array['pending_op_bot_id_4'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

    'pending_op_bot_id_5' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a bot'),
        'required' => false,
        'frontend' => 'name',
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'params_call' => array(),
        'main_attr' => 'bot_configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'pending_op_5_trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Choose a trigger'),
        'required' => false,
        'hidden' => true,
        'frontend' => 'name',
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'main_attr' => 'bot_configuration_array',
        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['pending_op_bot_id_5']) ? $this->bot_configuration_array['pending_op_bot_id_5'] : 0))),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),

);