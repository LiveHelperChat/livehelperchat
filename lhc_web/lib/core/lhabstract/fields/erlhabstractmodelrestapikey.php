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
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
    ),
    'active' => array(
        'type' => 'checkbox',
        'trans' => 'Active',
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
    )
);
