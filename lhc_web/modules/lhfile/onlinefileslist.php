<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/chatfileslist.tpl.php');
$tpl->set('items', erLhcoreClassChat::getList(array('filter' => array('online_user_id' => $Params['user_parameters']['online_user_id']),'limit' => 100,'sort' => 'id DESC'),'erLhcoreClassModelChatFile','lh_chat_file'));

echo json_encode(array('result' => $tpl->fetch()));
exit;

?>