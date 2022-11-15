<?php

$proactiveAttr = array(
    'event_id' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Event id to receive from website for conversion to finish.'),
        'required' => false,
        'hidden' => true,
        'maxlength' => 20,
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','E.g ordered'),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'conversion_expires_in' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Conversion has to happen within this period of time after invitation was send/clicked.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'name' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Name for personal purposes'),
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'operator_name' => array(
        'type' => 'text',
        'translatable' => true,
        'main_attr_lang' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Operator name'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'position' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Position'),
        'required' => true,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'siteaccess' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Language, leave empty for all. E.g lit, rus, ger etc...'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'time_on_site' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Time on site in seconds'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'delay' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Delay invitation widget show for N seconds if invitation was already matched.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'delay_init' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Delay invitation widget show for N seconds if trigger is matched for first time.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'show_instant' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'If dynamic invitation was matched on page refresh show instantly. Otherwise dynamic conditions will have to be matched again.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'inject_only_html' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Inject only HTML, widget state will not be changed. Matched invitation is executed on each page load.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'pageviews' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Pageviews'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'referrer' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Referrer domain without www, E.g google keyword will match any of google domain'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'hide_after_ntimes' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'How many times user show invitation, 0 - untill users closes it, > 0 limits.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'expires_after' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation expire time, after that period of time invitation will be hidden'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'on_op_id' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Any of these operators are online. Enter operator id separated by comma. Your user id is'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'op_max_chats' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator maximum chats limit can be exceeded by this number of chats.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'last_visit_prev' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Last time seen on website ago.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'last_chat' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Last time had chat n minutes ago.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'next_inv_time' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','After this invitation expires or was closed, any next invitation can be shown after defined amount of time.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'requires_email' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Requires e-mail'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'requires_username' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Requires name'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'show_on_mobile' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show on these devices only'),
        'required' => false,
        'hidden' => true,
        'name_attr' => 'name',
        'hide_optional' => true,
        'source' => 'erLhAbstractModelProactiveChatInvitation::getDeviceOptions',
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'assign_to_randomop' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Assign to shown operator'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'show_everytime' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show everytime it is matched'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'show_after_chat' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show invitation next time even if a customer started a chat'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'requires_phone' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Requires phone'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'show_random_operator' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show random operator profile'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'operator_ids' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Enter operators IDs from whom random operator should be shown, separated by comma'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string')
    ),
    'identifier' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Identifier, for what identifier this message should be shown, leave empty for all'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string')
    ),
    'tag' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Tag'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string')
    ),
    'autoresponder_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Auto responder to apply'),
        'required' => false,
        'hidden' => true,
        'source' => 'erLhAbstractModelAutoResponder::getList',
        'params_call' => array('filter' => array('user_id' => 0)),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'dep_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Department'),
        'required' => false,
        'hidden' => true,
        'source' => 'erLhcoreClassModelDepartament::getList',
        'hide_optional' => !empty($departmentFilterdefault = erLhcoreClassUserDep::conditionalDepartmentFilter()),
        'params_call' => array_merge(['limit' => false, 'sort' => '`name` ASC'],$departmentFilterdefault),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'parent_id' => array(
        'type' => 'text',
        'default_value' => 0,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Parent invitation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'campaign_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Campaign'),
        'required' => false,
        'hidden' => true,
        'source' => 'erLhAbstractModelProactiveChatCampaign::getList',
        'params_call' => ['limit' => false],
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'bot_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Bot'),
        'required' => false,
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotBot::getList',
        'hide_optional' => false,
        'params_call' => ['limit' => false, 'sort' => '`name` ASC'],
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'trigger_id' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Trigger to execute'),
        'required' => false,
        'hidden' => true,
        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
        'hide_optional' => false,
        'params_call' => array('filter' => array('bot_id' => $this->bot_id)),
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'bot_offline' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Execute bot only if there is no online operators'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'executed_times' => array(
        'type' => 'none',
        'hide_edit' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Matched times'),
        'required' => false,
        'link' => 'statistic/campaignmodal',
        'is_modal' => true,
        'is_iframe' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'message' => array(
        'type' => 'textarea',
        'help' => 'inv_message',
        'translatable' => true,
        'main_attr_lang' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to user'),
        'required' => true,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'message_returning' => array(
        'type' => 'textarea',
        'translatable' => true,
        'main_attr_lang' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Message to returning user'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'message_returning_nick' => array(
        'translatable' => true,
        'main_attr_lang' => 'design_data_array',
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Nick which will be used if we cannot determine returning user name'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'dynamic_invitation' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'This is dynamic invitation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    ),
    'event_type' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Choose a dynamic event'),
        'required' => false,
        'hidden' => true,
        'name_attr' => 'name',
        'params_call' => array(),
        'source' => 'erLhAbstractModelProactiveChatInvitation::getEventTypes',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'iddle_for' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show invitation if visitor is idle for n seconds'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'disabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Disabled'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'mobile_html_only' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Apply HTML invitation only to mobile devices'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'dynamic_everytime' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Inject HTML everytime dynamic event occurs'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'api_do_not_show' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Do not show widget automatically'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'full_on_invitation' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show widget on invitation to chat. Applies only to desktop devices.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'close_above_msg' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Close button above invitation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'photo_left_column' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'hidden' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show profile photo on the left'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'hide_op_name' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Hide operator name in invitation widget'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'hide_op_img' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Hide operator profile image'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'hide_on_open' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Hide content on click'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'std_header' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Show default header on proactive widget open event'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'play_sound' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Play sound'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'ignore_bot' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Skip bot and set chat status directly to pending state'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'append_bot' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Append trigger content in full widget'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'keep_after_close' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Keep invitation body after widget close. No further invitations will be processed.'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'append_intro_bot' => array(
        'type' => 'checkbox',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Append trigger content in intro message'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'message_width' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation message width'),
        'required' => false,
        'hidden' => true,
        'placeholder' => 200,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'message_bottom' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation message placement from bottom'),
        'required' => false,
        'hidden' => true,
        'placeholder' => 75,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'message_right' => array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation message placement from right'),
        'required' => false,
        'hidden' => true,
        'placeholder' => 75,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'mobile_html' => array(
        'type' => 'textarea',
        'main_attr' => 'design_data_array',
        'height' => '200px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Mobile HTML'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'custom_on_click' => array(
        'type' => 'textarea',
        'main_attr' => 'design_data_array',
        'height' => '200px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom on click event'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'inject_html' => array(
        'type' => 'textarea',
        'main_attr' => 'design_data_array',
        'height' => '200px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Inject HTML'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'mobile_style' => array(
        'type' => 'textarea',
        'ace_editor' => 'css',
        'placeholder' => '.proactive-need-help{background-color:black!important;padding:0px!important;}',
        'main_attr' => 'design_data_array',
        'height' => '200px',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom CSS, applies also to invitation bubble'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'design_data_img_1' => array(
        'type' => 'file',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom image 1'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'design_data_array',
        'frontend' => 'design_data_img_1_url_img',
        'backend_call' => 'movePhoto',
        'backend_call_param' => 'design_data_img_1',
        'delete_call' => 'deletePhoto',
        'delete_call_param' => 'design_data_img_1',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
    )),
    'design_data_img_2' => array(
        'type' => 'file',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom image 2'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'design_data_array',
        'frontend' => 'design_data_img_2_url_img',
        'backend_call' => 'movePhoto',
        'backend_call_param' => 'design_data_img_2',
        'delete_call' => 'deletePhoto',
        'delete_call_param' => 'design_data_img_2',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
    )),
    'design_data_img_3' => array(
        'type' => 'file',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom image 3'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'design_data_array',
        'frontend' => 'design_data_img_3_url_img',
        'backend_call' => 'movePhoto',
        'backend_call_param' => 'design_data_img_3',
        'delete_call' => 'deletePhoto',
        'delete_call_param' => 'design_data_img_3',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
    )),
    'design_data_img_4' => array(
        'type' => 'file',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom image 4'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'design_data_array',
        'frontend' => 'design_data_img_4_url_img',
        'backend_call' => 'movePhoto',
        'backend_call_param' => 'design_data_img_4',
        'delete_call' => 'deletePhoto',
        'delete_call_param' => 'design_data_img_4',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
    )),
    'design_data_img_5' => array(
        'type' => 'file',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom image 5'),
        'required' => false,
        'hidden' => true,
        'main_attr' => 'design_data_array',
        'frontend' => 'design_data_img_5_url_img',
        'backend_call' => 'movePhoto',
        'backend_call_param' => 'design_data_img_5',
        'delete_call' => 'deletePhoto',
        'delete_call_param' => 'design_data_img_5',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
    )),
);

for ($ai = 0; $ai < 10; $ai++) {
     $proactiveAttr['attrf_key_' . ($ai  + 1)] = array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => '',
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    );
    $proactiveAttr['attrf_val_' . ($ai  + 1)] = array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => '',
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    );
    $proactiveAttr['attrf_cond_' . ($ai  + 1)] = array(
        'type' => 'text',
        'main_attr' => 'design_data_array',
        'trans' => '',
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    );
}

return $proactiveAttr;