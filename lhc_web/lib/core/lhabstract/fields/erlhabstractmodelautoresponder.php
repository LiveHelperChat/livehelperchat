<?php 

return array(
   				'siteaccess' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Language, leave empty for all. E.g lit, rus, ger etc...'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'position' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Position'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'dep_id' => array (
   						'type' => 'combobox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Department'),
   						'required' => false,
   						'hidden' => true,
   						'source' => 'erLhcoreClassModelDepartament::getList',
   						'hide_optional' => false,
   						'params_call' => array(),
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'wait_message' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Wait message. Visible when users starts chat and is waiting for someone to accept a chat.'),
   						'required' => false,
   						'hidden' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Wait timeout. Time in seconds before timeout message is shown.'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'repeat_number' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','How many times repeat message?'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   						)),
   				'timeout_message' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor this message when wait timeout passes'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout_reply_1' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor message when visitor does not respond for n seconds. [1]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   						)),
   				'timeout_reply_message_1' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message for timeout [1]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout_reply_2' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor message when visitor does not respond for n seconds. [2]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   						)),
   				'timeout_reply_message_2' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message for timeout [2]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout_reply_3' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor message when visitor does not respond for n seconds. [3]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   						)),
   				'timeout_reply_message_3' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message for timeout [3]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout_reply_4' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor message when visitor does not respond for n seconds. [4]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   						)),
   				'timeout_reply_message_4' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message for timeout [4]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout_reply_5' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor message when visitor does not respond for n seconds. [5]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   						)),
   				'timeout_reply_message_5' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message for timeout [5]'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   		);