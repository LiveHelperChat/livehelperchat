<?php
header('content-type: application/json; charset=utf-8');

$chats = erLhcoreClassChat::getActiveChats(10,0,array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())));

erLhcoreClassChat::prefillGetAttributes($chats,array('id','nick'),array(),array('remove_all' => true));

echo json_encode(array('result' => array_values($chats)));

exit;

?>