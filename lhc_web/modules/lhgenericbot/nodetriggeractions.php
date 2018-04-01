<?php

header ( 'content-type: application/json; charset=utf-8' );

$types = array(
    'text' => 'Send text',
    'generic' => 'Send Carrousel',
    'list' => 'Send List',
);

echo json_encode(
    array(
        'name' => 'Trigger one - ' . $Params['user_parameters']['id'],
        'id' => $Params['user_parameters']['id'],
        'group_id' => 1,
        'actions' => array(
            array(
                'type' => 'text',
                'text' => 'text'
            ),
            array(
                'type' => 'list',
                'text' => 'text2'
            )
        )
    )
);

/*
    <option value="text">Send Text</option>
    <option value="typing">Send Typing</option>
    <option value="predefined">Send predefined block</option>
    <option value="image">Send Image</option>
    <option value="video">Send Video</option>
    <option value="audio">Send Audio</option>
    <option value="file">Send File</option>
    <option value="button">Send Buttons</option>
    <option value="generic">Send Carrousel</option>
    <option value="list">Send List</option>
    <option value="command">Update Current Lead</option>
    <option value="callback" disabled="">Closure</option>
*/



exit;
?>