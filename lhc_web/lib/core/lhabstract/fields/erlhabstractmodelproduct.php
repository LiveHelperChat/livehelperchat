<?php 

return array(
		'name' => array(
				'type' => 'text',
				'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/product','Name'),
				'required' => true,   				    
				'validation_definition' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
		)),
		'priority' => array(
				'type' => 'text',
				'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/product','Priority'),
				'required' => false,
				'validation_definition' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
		)),
        'disabled' => array(
   		        'type' => 'checkbox',
   		        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Disabled'),
   		        'required' => false,           		        
   		        'validation_definition' => new ezcInputFormDefinitionElement(
   		            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
		'departament_id' => array (
        	    'type' => 'combobox',
        	    'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Department'),
        	    'required' => true,
        	    'frontend' => 'departament',
        	    'source' => 'erLhcoreClassModelDepartament::getList',                	    
        	    'params_call' => array(),
        	    'validation_definition' => new ezcInputFormDefinitionElement(
        	        ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ))
);