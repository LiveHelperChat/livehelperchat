<?php 

$fields = array(
   				'name' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Name'),
   						'required' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'alias' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Alias for argument. No spaces or slashes.'),
   						'required' => false,
                    'hidden' => true,
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
                        'hidden' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Chat status if customer is chatting with a bot'),
                        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Chat status if customer is chatting with a bot'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                    'icons_order' => array(
   						'type' => 'text',
                        'hidden' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Header icons order. _print is optional and indicates we should also print a text after an icon'),
                        'placeholder' => 'left_close<_print>,right_min,right_popup',
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
                'hide_mobile_nh' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide need help widget for mobile devices.'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                    )),
                'always_present_nh' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Always visible. Usefull if you make custom HTML and want that need help would be always visible.'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                    )),
                'hide_close_nh' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide close button'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
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
                    'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'offline_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline status text [old widget]'),
   						'required' => false,
                        'hidden' => true,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'intro_operator_text' => array(
                    'type' => 'text',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator intro text'),
                    'translatable' => true,
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Have a question? Ask us!'),
                    'main_attr_lang' => 'bot_configuration_array',
                    'required' => false,
                    'nginit' => true,
                    'hidden' => true,
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text/Icon color'),
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
                'disable_sound' => array(
   						'type' => 'checkbox',
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Disable sound for the visitor by default'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
                'hide_iframe' => array(
   						'type' => 'checkbox',
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Terminate script if parent window already has live help script'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
                'hide_parent' => array(
   						'type' => 'checkbox',
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Terminate script in parent window if any child iframe has already live helper script'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
                'kcw' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If visitor opens a popup keep chat in the widget also'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
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
                'intro_message' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator intro message. You can wrap custom HTML with [html][/html] bbcode'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'intro_message_html' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator intro message in HTML'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                 'pre_chat_html' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html above status/profile body in online mode'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'pre_offline_chat_html' => array(
   						'type' => 'textarea',
   						'height' => '50px',
                        'ace_editor' => 'html',
                        'translatable' => true,
                        'main_attr' => 'bot_configuration_array',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom html above status/profile body in offline mode'),
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
                  'custom_tos_text' => array(
                      'type' => 'text',
                      'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','TOS text'),
                      'required' => false,
                      'hidden' => true,
                      'translatable' => true,
                      'main_attr' => 'bot_configuration_array',
                      'validation_definition' => new ezcInputFormDefinitionElement(
                          ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                      )),
                'min_text' => array(
                      'type' => 'text',
                      'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title of the minimize icon'),
                      'required' => false,
                      'hidden' => true,
                      'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Minimize'),
                      'translatable' => true,
                      'main_attr' => 'bot_configuration_array',
                      'validation_definition' => new ezcInputFormDefinitionElement(
                          ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                      )),
                'popup_text' => array(
                      'type' => 'text',
                      'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title of the popup icon'),
                      'required' => false,
                      'hidden' => true,
                      'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Popup'),
                      'translatable' => true,
                      'main_attr' => 'bot_configuration_array',
                      'validation_definition' => new ezcInputFormDefinitionElement(
                          ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                      )),
                'end_chat_text' => array(
                      'type' => 'text',
                      'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Title of the end chat icon'),
                      'required' => false,
                      'hidden' => true,
                      'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','End chat'),
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat and is waiting for operator to join a chat. Only if queue is 1 or if less than a minute wait time.'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending a support staff member to join, you can write your questions, and as soon as a support staff member confirms this chat, they will get your messages OR Less than a minute'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
                'pending_join_queue' => array(
   						'type' => 'text',
   						'main_attr_lang' => 'bot_configuration_array',
   						'translatable' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat and is waiting for operator to join a chat. Only if queue is > 1. {number}, {avg_wait_time}, {avg_wait_time_live}, {avg_wait_time_live__string if more than one minute live wait time}, {avg_wait_time__string if more than one minute wait time}'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','You are number {number} in the queue. Please wait... OR Average waiting time for attention is {avg_wait_time} minute{avg_wait_time__s}'),
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
                'formf_name' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for the name field'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'formf_email' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for the e-mail field'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'E-mail'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'formf_file' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for the file field'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'formf_phone' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for the phone field'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'formf_question' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for the question field'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
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

                'operator_avatar' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator avatar'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                    )),

                'nh_avatar' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help avatar'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                    )),

                'wright' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget position from right to append'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'wbottom' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget position from bottom to append'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'wright_inv' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget position from right to append in invitation'),
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
                'custom_op_name' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Main operator title, {nick}, {name}, {surname}'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','{nick}'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'chat_unavailable' => array(
                    'type' => 'text',
                    'main_attr' => 'bot_configuration_array',
                    'translatable' => true,
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Chat is unavailable and offline form is disabled.'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Chat is currently unavailable. Please try again later.'),
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
                'start_on_close' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show start chat form instantly after operator closes a chat.'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'prev_msg' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show previous chat messages in chat widget.'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'custom_html_priority' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom HTML has priority over invitation content in opened widget'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'proactive_once_typed' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show invitation content once visitor started to type'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'hide_job_title' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide job title'),
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
                'dont_prefill_offline' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Do not prefill offline message with chat messages.'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'hide_bb_code' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Hide BB Code button'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'msg_snippet' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show new messages snippet widget'),
                    'required' => false,
                    'hidden' => true,
                    'nginit' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'font_size' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Allow visitor to change font size'),
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
                'fscreen_embed' => array(
                    'type' => 'checkbox',
                    'main_attr' => 'bot_configuration_array',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Try to expand widget to full screen in page embed mode'),
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
    'offl_bcolor' => array(
        'type' => 'colorpicker',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline widget background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'offlbor_bcolor' => array(
        'type' => 'colorpicker',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline widget border color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'offltxt_color' => array(
        'type' => 'colorpicker',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline text/icon color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'close_in_status' => array(
        'type' => 'checkbox',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show close widget button in status widget'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'job_new_row' => array(
        'type' => 'checkbox',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show operator profile in a new row'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

    'enable_widget_embed_override' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Enable embed code override'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'widget_show_leave_form' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Show a leave a message form when there are no online operators'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'widget_survey' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Survey at the end of chat'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    ),
    'widget_position' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Position'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'widget_popwidth' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Popup window width in pixels'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'widget_popheight' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Popup window height in pixels'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'widget_pright' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Widget status position from right or left depending on main position. E.g 10 or -10'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'widget_pbottom' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'Widget status position from bottom. E.g 10 or -10'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'cnew_msg' => array(
        'type' => 'text',
        'main_attr' => 'bot_configuration_array',
        'translatable' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','New messages text. 1 message case'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','New message!'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'cnew_msgm' => array(
        'type' => 'text',
        'main_attr' => 'bot_configuration_array',
        'translatable' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','New messages text. 2 or more new messages'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','New messages!'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'cscroll_btn' => array(
        'type' => 'text',
        'main_attr' => 'bot_configuration_array',
        'translatable' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Scroll to the bottom'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','↓ Scroll to the bottom'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'bg_scroll_bottom' => array(
        'type' => 'colorpicker',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Scroll to the bottom background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'text_scroll_bottom' => array(
        'type' => 'colorpicker',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'embed_closed' => array(
        'type' => 'text',
        'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Close button position in embed mode'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    // New message indicator
    'cnew_msgh' => array(
        'type' => 'text',
        'main_attr' => 'bot_configuration_array',
        'translatable' => true,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','New message text'),
        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','New'),
        'required' => false,
        'hidden' => true,
        'nginit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
     'new_msg_text_color' => array(
        'type' => 'colorpicker',
         'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','New message text color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),

     'bg_new_msg' => array(
        'type' => 'colorpicker',
         'main_attr' => 'bot_configuration_array',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','New message background color'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),


);
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.fields',array('fields' => & $fields));

return $fields;