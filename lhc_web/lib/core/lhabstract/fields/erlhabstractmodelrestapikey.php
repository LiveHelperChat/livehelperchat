<?php

return array(
    'user_id' => array(
        'type' => 'combobox',
        'trans' => 'Username',
        'required' => true,
        'frontend' => 'username',
        'params_call' => array(),
        'source' => 'erLhcoreClassModelUser::getUserList',
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::REQUIRED, 'int')
    ),
    'api_key' => array(
        'type' => 'text',
        'trans' => 'API Key, max 50 characters',
        'maxlength' => 50,
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'ip_restrictions' => array(
        'type' => 'text',
        'trans' => 'IP restrictions. E.g 1.2.3.*,1.2.3/24,1.2.3.4/255.255.255.0,1.2.3.0-1.2.3.255',
        'required' => true,
        'maxlength' => 250,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'active' => array(
        'type' => 'checkbox',
        'trans' => 'Active',
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    )
);
