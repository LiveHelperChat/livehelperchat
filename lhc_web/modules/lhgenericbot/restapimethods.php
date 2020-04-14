<?php

header ( 'content-type: application/json; charset=utf-8' );

$restAPI = erLhcoreClassModelGenericBotRestAPI::fetch((int)$Params['user_parameters']['id']);

$methods = array();

if (isset($restAPI->configuration_array['parameters'])){
    foreach ($restAPI->configuration_array['parameters'] as $parameter) {
        $methods[] = array(
            'name' => isset($parameter['name']) ? $parameter['name'] : 'Unknown',
            'userparams' =>  $parameter['userparams'],
            'id' =>  $parameter['id'],
            'output' => isset($parameter['output']) ? $parameter['output'] : [],
        );
    }
}

echo json_encode(array(
    'methods' => $methods,
    'id' => $restAPI->id
));

exit();

?>