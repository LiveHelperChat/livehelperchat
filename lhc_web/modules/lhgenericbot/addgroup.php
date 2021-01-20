<?php

header ( 'content-type: application/json; charset=utf-8' );

$group = new erLhcoreClassModelGenericBotGroup();
$group->name = "New group";
$group->bot_id = (int)$Params['user_parameters']['id'];
$group->saveThis();

echo json_encode(
    array(
        'name' => $group->name,
        'id' => $group->id,
        'pos' => $group->pos,
        'is_collapsed' => $group->is_collapsed,
        'bot_id' => $group->bot_id,
    )
);

exit;
?>