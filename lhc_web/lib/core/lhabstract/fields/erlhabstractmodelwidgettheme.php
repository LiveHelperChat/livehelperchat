<?php 

$fields = array(
   				'name' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Name'),
   						'required' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'name_company' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Name of the company'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'bot_status_text' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Chat status if customer is chatting with a bot'),
                        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Chat status if customer is chatting with a bot'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_header' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help header text'),
                        'translatable' => true,
   						'required' => false,   
   						'nginit' => true,	
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   			
   				'show_need_help_timeout' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help tooltip timeout, after how many hours show again tooltip?'),
   						'required' => false,   
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'show_need_help_delay' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','After how many seconds after page load show need help tooltip?'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'show_status_delay' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','After how many seconds after page load show status widget'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),

                'nh_width' => array(
                    'type' => 'text',
                    'placeholder' => '320',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help widget width'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'string'
                    )),
                
                'nh_height' => array(
                    'type' => 'text',
                    'placeholder' => '135',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help widget height'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'string'
                    )),
                
                'nh_right' => array(
                    'type' => 'text',
                    'placeholder' => '45',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Position from right'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'string'
                    )),
                
                'nh_bottom' => array(
                    'type' => 'text',
                    'placeholder' => '70',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Position from bottom'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'string'
                    )),

   				'show_need_help' => array(
   				        'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show need help tooltip?'),
   						'required' => false,
   						'hidden' => true,   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
                'hide_ts' => array(
   				        'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide message time from visitor'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
                'modern_look' => array(
   				        'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use modern look. It is used only in older widget.'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
                    'load_w2' => array(
                        'type' => 'checkbox',
                        'main_attr' => 'bot_configuration_array',
                        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use a new widget look for old embed code. If you can not change easily old embed codes you can force system to load new widget still.'),
                        'required' => false,
                        'hidden' => true,
                        'nginit' => true,
                        'validation_definition' => new ezcInputFormDefinitionElement(
                            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                        )),
   				'need_help_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help standard text'),
                        'translatable' => true,
                        'main_attr_lang' => 'bot_configuration_array',
   						'required' => false,
   						'nginit' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'widget_border_width' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget border width (px)'),
   						'placeholder' => 1,
   						'required' => false,
   						'nginit' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'online_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online status text [old widget]'),
   						'required' => false,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'offline_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline status text [old widget]'),
   						'required' => false,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'intro_operator_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator intro text'),
   						'required' => false,   						
   						'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Have a question? Ask us!'),   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'onl_bcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'bor_bcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget border color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'text_color' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text color'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'operator_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator image in chat widget'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'operator_image_url_img',
   						'backend_call' => 'movePhoto',
       				    'backend_call_param' => 'operator_image',
       				    'delete_call' => 'deletePhoto',
       				    'delete_call_param' => 'operator_image',   				    
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   				
   				'logo_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Logo image, visible in popup'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'logo_image_url_img',   						   				    
   				        'backend_call' => 'movePhoto',
   				        'backend_call_param' => 'logo_image',
   				        'delete_call' => 'deletePhoto',
   				        'delete_call_param' => 'logo_image',   				    
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   				
   				'copyright_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Logo image, visible in widget left corner, 16x16'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'copyright_image_url_img',   				    
       				    'backend_call' => 'movePhoto',
       				    'backend_call_param' => 'copyright_image',
       				    'delete_call' => 'deletePhoto',
       				    'delete_call_param' => 'copyright_image',   				    
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   				
   				'show_copyright' => array(
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show copyright widget logo in left corner'),
   						'required' => false,
   						'hidden' => true,   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)), 	
   				'hide_close' => array(
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide widget close button'),
   						'required' => false,
   						'hidden' => true,   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)), 	
   				'hide_popup' => array(
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide popup option'),
   						'required' => false,
   						'hidden' => true,   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)), 	
   				'header_height' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header height (px)'),
   				        'placeholder' => 15,
   						'required' => false,  
   						'hidden' => true,
   						'nginit' => true,						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'widget_response_width' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget response layout width trigger (px)'),
   				        'placeholder' => 640,
   						'required' => false,
   						'hidden' => true,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'header_padding' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header padding (px)'),
   						'required' => false,
   				        'placeholder' => 5,
   						'hidden' => true,
   						'nginit' => true,						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),  				
   				'widget_copyright_url' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Site URL'),
   						'required' => false,  
   						'hidden' => true,
   						'nginit' => true,						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),  				
   				'explain_text' => array(
   						'type' => 'textarea',
                        'translatable' => true,
                        'main_attr_lang' => 'bot_configuration_array',
   						'height' => '50px',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text above start chat form fields'),
   						'required' => false,  
   						'hidden' => true,
   						'nginit' => true,						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html before start chat form fields, popup'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                    'inject_html' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Inject HTML on widget open'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'header_html' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header HTML. Here you can paste custom head HTML.'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html_widget' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'main_attr' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html before start chat form fields, widget'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                    'custom_page_css' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'css',
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom page CSS (new widget only)'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html_bot' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html before start chat form fields, popup (bot mode)'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html_widget_bot' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html before start chat form fields, widget (bot mode)'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html_header' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html before standard widget header'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html_header_body' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html inside standard widget header'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_html_status' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html before standard widget status header'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_start_button' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Start chat button text, standard chat'),
   						'required' => false,
   						'hidden' => true,
                        'translatable' => true,
   						'main_attr' => 'bot_configuration_array',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'custom_start_button_bot' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Start chat button text, bot chat'),
   						'required' => false,
   						'hidden' => true,
                        'translatable' => true,
   						'main_attr' => 'bot_configuration_array',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'bot_id' => array(
   						'type' => 'combobox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose a bot'),
   						'required' => false,
                        'frontend' => 'name',
   						'hidden' => true,
                        'source' => 'erLhcoreClassModelGenericBotBot::getList',
                        'params_call' => array(),
   						'main_attr' => 'bot_configuration_array',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),
                'trigger_id' => array(
   						'type' => 'combobox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose a trigger'),
   						'required' => false,
   						'hidden' => true,
                        'frontend' => 'name',
                        'source' => 'erLhcoreClassModelGenericBotTrigger::getList',
   						'main_attr' => 'bot_configuration_array',
                        'params_call' => array('filter' => array('bot_id' => (isset($this->bot_configuration_array['bot_id']) ? $this->bot_configuration_array['bot_id'] : 0))),
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),
                'custom_start_button_offline' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Leave a message button text'),
   						'required' => false,
   						'hidden' => true,
   						'main_attr' => 'bot_configuration_array',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'online_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online image'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'online_image_url_img',   				    
       				    'backend_call' => 'movePhoto',
       				    'backend_call_param' => 'online_image',
       				    'delete_call' => 'deletePhoto',
       				    'delete_call_param' => 'online_image',   				    
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),
                'notification_icon' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Notification icon'),
   						'required' => false,
   						'hidden' => true,
                        'main_attr' => 'notification_configuration_array',
   						'frontend' => 'notification_icon_url_img',
       				    'backend_call' => 'movePhoto',
       				    'backend_call_param' => 'notification_icon',
       				    'delete_call' => 'deletePhoto',
       				    'delete_call_param' => 'notification_icon',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),
   				'offline_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline image'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'offline_image_url_img',   						   				    
       				    'backend_call' => 'movePhoto',
       				    'backend_call_param' => 'offline_image',
       				    'delete_call' => 'deletePhoto',
       				    'delete_call_param' => 'offline_image',   				    
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   			
   				'header_background' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget header background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'widget_border_color' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget border color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),

            'need_help_html' => array(
                'type' => 'textarea',
                'main_attr' => 'bot_configuration_array',
                'translatable' => true,
                'ace_editor' => 'html',
                'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom need help HTML'),
                'required' => false,
                'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Custom need help HTML'),
                'hidden' => true,
                'validation_definition' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),

    'need_help_bcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_hover_bg' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help hover background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_tcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help text color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'support_joined' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts chat based on proactive invitation'),
   						'required' => false,
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has joined this chat'),
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'support_closed' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when operator closes a chat'),
   						'required' => false,
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has closed this chat'),
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'pending_join' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat and is waiting for operator to join a chat. Only if queue is 1'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending a support staff member to join, you can write your questions, and as soon as a support staff member confirms this chat, they will get your messages'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'pending_join_queue' => array(
   						'type' => 'text',
   						'main_attr_lang' => 'bot_configuration_array',
   						'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat and is waiting for operator to join a chat. Only if queue is >= 1'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','You are number {number} in the queue. Please wait...'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'noonline_operators' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat but department is offline'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged in support staff members, but you can leave your messages'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'noonline_operators_offline' => array(
   						'type' => 'text',
                        'main_attr_lang' => 'bot_configuration_array',
                        'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Leave a message form text'),
   						'required' => false,
   						'hidden' => true,
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There are no online operators at the moment, please leave a message'),
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),

                'thank_feedback' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Thank you for your feedback text'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Thank you for your feedback'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                    )),

   				'need_help_border' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help border color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_close_bg' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help close background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_close_hover_bg' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help close hover background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_image' => array(
   						'type' => 'file',
   						'frontend' => 'need_help_image_url_img',   						   				    
       				    'backend_call' => 'movePhoto',
       				    'backend_call_param' => 'need_help_image',
       				    'delete_call' => 'deletePhoto',
       				    'delete_call_param' => 'need_help_image',   				    
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help operator image'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								 ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),
   				'custom_status_css' => array(
   						'type' => 'textarea',
   						'ace_editor' => 'css',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget additional CSS, takes effect after save'),
   						'required' => true,
   						'placeholder' => '#lhc_status_container:hover{}',
   						'hidden' => true,
   						'height' => '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_popup_css' => array(
   						'type' => 'textarea',
                        'ace_editor' => 'css',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom CSS only for popup'),
   						'required' => true,
   						'placeholder' => 'body(background-color:red)',
   						'hidden' => true,
   						'height' => '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_container_css' => array(
   						'type' 			=> 'textarea',
                        'ace_editor'    => 'css',
   						'trans' 		=> erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget container additional CSS, takes effect after save'),
   						'required' 		=> true,
   						'hidden' 		=> true,
   						'placeholder'	=>'#lhc_container #lhc_iframe_container{border:0};',
   						'height' => '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_widget_css' => array(
   						'type' 			=> 'textarea',
                        'ace_editor'    => 'css',
   						'trans' 		=> erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget body additional CSS, takes effect after save'),
   						'required' 		=> true,
   						'placeholder' 	=> 'body {background-color:#84A52E;}',
   						'hidden' 		=> true,
   						'height' 		=> '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   		       'minimize_image' => array(
           		        'type' => 'file',
           		        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Minimize image'),
           		        'required' => false,
           		        'hidden' => true,
           		        'frontend' => 'minimize_image_url_img',
           		        'backend_call' => 'movePhoto',
   		                'backend_call_param' => 'minimize_image',
           		        'delete_call' => 'deletePhoto',
   		                'delete_call_param' => 'minimize_image',
           		        'validation_definition' => new ezcInputFormDefinitionElement(
           		            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   		        )),
   		       'restore_image' => array(
           		        'type' => 'file',
           		        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Restore image'),
           		        'required' => false,
           		        'hidden' => true,
           		        'frontend' => 'restore_image_url_img',
           		        'backend_call' => 'movePhoto',  
   		                'backend_call_param' => 'restore_image',
   		                'delete_call' => 'deletePhoto',
   		                'delete_call_param' => 'restore_image',
           		        'validation_definition' => new ezcInputFormDefinitionElement(
           		            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   		        )),
   		       'close_image' => array(
           		        'type' => 'file',
           		        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Close image'),
           		        'required' => false,
           		        'hidden' => true,
           		        'frontend' => 'close_image_url_img',
           		        'backend_call' => 'movePhoto',
           		        'backend_call_param' => 'close_image',
           		        'delete_call' => 'deletePhoto',
           		        'delete_call_param' => 'close_image',
           		        'validation_definition' => new ezcInputFormDefinitionElement(
           		            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   		        )),
   		       'popup_image' => array(
           		        'type' => 'file',
           		        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Popup image'),
           		        'required' => false,
           		        'hidden' => true,
           		        'frontend' => 'popup_image_url_img',
           		        'backend_call' => 'movePhoto',
           		        'backend_call_param' => 'popup_image',
           		        'delete_call' => 'deletePhoto',
           		        'delete_call_param' => 'popup_image',
           		        'validation_definition' => new ezcInputFormDefinitionElement(
           		            ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   		        )),
                // Visitor messages style
                'buble_visitor_background' => array(
                    'type' => 'colorpicker',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor bubble background color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'buble_visitor_title_color' => array(
                    'type' => 'colorpicker',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor title color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'buble_visitor_text_color' => array(
                    'type' => 'colorpicker',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor text color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                // Operator messages style
                'buble_operator_background' => array(
                    'type' => 'colorpicker',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator bubble background color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'buble_operator_title_color' => array(
                    'type' => 'colorpicker',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator title color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'buble_operator_text_color' => array(
                    'type' => 'colorpicker',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator text color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
    
                // General chat settings
                'show_voting' => array(
                    'type' => 'checkbox',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show voting thumbs?'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
                )),
                'department_title' => array(
                    'type' => 'text',
                    'main_attr_lang' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for department? E.g Location'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Location'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'department_select' => array(
                    'type' => 'text',
                    'main_attr_lang' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Additional option before department selection'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose department'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_border' => array(
                    'type' => 'colorpicker',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button border color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_background' => array(
                    'type' => 'colorpicker',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button background color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_background_hover' => array(
                    'type' => 'colorpicker',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button background hover color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_text_color' => array(
                    'type' => 'colorpicker',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button text color'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_border_radius' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button border radius'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_padding' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button top and bottom'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_padding_left_right' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button padding right and left'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bot_button_fs' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Button font size'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'wheight' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget height (px)'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'wwidth' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget width (px)'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'switch_to_human' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','After how many user messages show switch to human button. empty - never, 0 - always'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'placeholder_message' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Placeholder for message text'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Type your message here and hit enter to send...'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'disable_edit_prev' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Do not allow visitor to edit previous message by pressing up arrow'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'confirm_close' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Before closing chat ask user does he really want to to close chat'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'close_on_unload' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Close chat if page is refreshed. Usefull if you have embed code in popup.'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'survey_button' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show go to survey button on chat close'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'auto_bot_intro' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Automatically determine intro message by bot default message'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'detect_language' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Try to detect language from browser headers'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'bubble_style_profile' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Bubble style messages'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'hide_status' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide chat status block'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'msg_expand' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use expanding message area'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'hide_visitor_profile' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide visitor profile'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'notification_enabled' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'notification_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Notifications enabled'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
                )),
                'ntitle' => array(
                    'type' => 'text',
                    'main_attr' => 'notification_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Notification title'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'ndomain' => array(
                    'type' => 'text',
                    'main_attr' => 'notification_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Notification domain'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
       'header_icon_color' => array(
            'type' => 'colorpicker',
            'main_attr' => 'bot_configuration_array',
            'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header icons color'),
            'required' => true,
            'hidden' => true,
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
       ),
);
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.fields',array('fields' => & $fields));

return $fields;