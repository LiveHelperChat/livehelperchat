<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch($Params['user_parameters']['id']);

echo json_encode(
    array(
        'name' => $trigger->name,
        'id' => $trigger->id,
        'group_id' => $trigger->group_id,
        'actions' => $trigger->actions_front
    )
);


/*array(
    array(
        'type' => 'text',
        'text' => 'text'
    ),
    array(
        'type' => 'list',
        'text' => 'text2'
    )
)*/


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