<?php

header('content-type: application/json; charset=utf-8');

$arguments = array();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_arguments', array(
    'arguments' => & $arguments
));

echo json_encode(array('arguments' => $arguments));

/* array(
        'callback_status' =>
    array(
        'name' => 'Call back status',
        'items' => array(
            array(
                'name' => 'Success name 1',
                'placeholder' => 'place holder',
                'id' => 'a00001'
            ),
            array(
                'name' => 'Success name 2',
                'placeholder' => 'place holder',
                'id' => 'a00002'
            )
        )
    ),
    'callback_status_2' =>
    array(
        'name' => 'Call back status two',
        'items' => array(
            array(
                'name' => 'Success name 1',
                'placeholder' => 'place holder',
                'id' => 'b00001'
            ),
            array(
                'name' => 'Success name 2',
                'placeholder' => 'place holder',
                'id' => 'b00002'
            ),
            array(
                'name' => 'Success name 2',
                'placeholder' => 'place holder',
                'id' => 'b00003'
            )
        )
    )*/
exit;
?>