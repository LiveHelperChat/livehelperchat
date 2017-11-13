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
   				'need_help_header' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help header text'),
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
   				'need_help_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help standard text'),
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online status text'),
   						'required' => false,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'offline_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline status text'),
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
   						'height' => '50px',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text above start chat form fields'),
   						'required' => false,  
   						'hidden' => true,
   						'nginit' => true,						
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts chat based on proactive invitation'),
   						'required' => false,
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has joined this chat'),
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'support_closed' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when operator closes a chat'),
   						'required' => false,
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','A support staff member has closed this chat'),
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'pending_join' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat and is waiting for operator to join a chat'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending a support staff member to join, you can write your questions, and as soon as a support staff member confirms this chat, they will get your messages'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'noonline_operators' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text when user starts a chat but department is offline'),
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged in support staff members, but you can leave your messages'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),    
   				'noonline_operators_offline' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Leave a message form text'),
   						'required' => false,
   						'hidden' => true,
   				        'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There are no online operators at the moment, please leave a message'),
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Background color for popup'),
   						'required' => true,
   						'placeholder' => 'body(background-color:red)',
   						'hidden' => true,
   						'height' => '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_container_css' => array(
   						'type' 			=> 'textarea',
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
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor buble background color'),
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
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator buble background color'),
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
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Use different title for department? E.g Location'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Location'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
                'department_select' => array(
                    'type' => 'text',
                    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Additional option before department selection'),
                    'placeholder' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose department'),
                    'required' => false,
                    'hidden' => true,
                    'validation_definition' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                )),
   		);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.fields',array('fields' => & $fields));

return $fields;