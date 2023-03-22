<?php 

$surveyFields = array(
    'id' => array(
        'type' => 'none',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','ID'),
        'required' => false,
        'width' => '1',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'name' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Name'),
        'required' => false,
        'link' => erLhcoreClassDesign::baseurl('survey/collected'),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'identifier' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Identifier'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'feedback_text' => array(
        'type' => 'textarea',
        'height' => '86px',
        'main_attr_lang' => 'configuration_array',
        'translatable' => true,
        'hidden' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Feedback text'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Thank you for your feedback...'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'survey_title' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Survey title'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Please complete this short evaluation survey'),
        'required' => false,
        'hidden' => true,
        'translatable' => true,
        'main_attr' => 'configuration_array',
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'disable_chat_preview' => array(
        'type' => 'checkbox',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Disable chat preview'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'return_on_close' => array(
        'type' => 'checkbox',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Allow visitor return to chat even if it was closed.'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'disable_chat_download' => array(
        'type' => 'checkbox',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Disable chat download'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_1' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars for feedback'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_1_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars pos'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_1_title' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_1_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_1_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars for feedback'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
	'max_stars_2_pos' => array(
			'type' => 'text',
			'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars pos'),
			'required' => false,
			'hidden' => true,
			'validation_definition' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
					)),
    'max_stars_2_title' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_2_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_2_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars for feedback'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
	'max_stars_3_pos' => array(
			'type' => 'text',
			'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars pos'),
			'required' => false,
			'hidden' => true,
			'validation_definition' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
					)),
    'max_stars_3_title' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_3_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_3_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars for feedback'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
	'max_stars_4_pos' => array(
			'type' => 'text',
			'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars pos'),
			'required' => false,
			'hidden' => true,
			'validation_definition' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
					)),
    'max_stars_4_title' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_4_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_4_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars for feedback'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
	'max_stars_5_pos' => array(
			'type' => 'text',
			'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Max stars pos'),
			'required' => false,
			'hidden' => true,
			'validation_definition' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
					)),
    'max_stars_5_title' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'max_stars_5_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for evaluation'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'max_stars_5_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_1' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_1_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_1_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_1_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_2_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_2_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_2_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_3_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_3_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_3_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_4_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_plain_4_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_4_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_plain_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )), 
    'question_plain_5_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )), 
    'question_plain_5_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for plain question'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )), 
    'question_plain_5_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )), 
    // Question with answer options
    'question_options_1' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'hidden' => true,
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_1_items' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'hidden' => true,
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_1_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'hidden' => true,
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_1_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'hidden' => true,
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_1_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'hidden' => true,
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_2' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_2_items' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_2_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_2_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_2_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_3' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_3_items' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_3_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_3_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_3_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_4' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),		
    'question_options_4_items' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),		
    'question_options_4_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),		
    'question_options_4_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),		
    'question_options_4_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),		
    'question_options_5' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_5_items' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_5_pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'question_options_5_enabled' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Title for custom questions'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'question_options_5_req' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Required'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
);

for ($i = 1; $i < 5; $i++) {

    $surveyFields['min_stars_question_plain_' . $i] = array(
        'type' => 'text',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme',' Required only if stars less or equal to 1 - n'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min' => 1)
        ));

    $surveyFields['star_field_question_plain_' . $i] = array(
        'type' => 'text',
        'main_attr' => 'configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Stars field Nr. (1-5)'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min' => 1, 'max' => 5)
        ));
}



return $surveyFields;
?>