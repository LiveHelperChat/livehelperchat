<?php 

$online_user = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters']['vid']);

if ($online_user !== false) 
{
    $tpl = erLhcoreClassTemplate::getInstance( 'lhfile/chatfileslistuser.tpl.php');
    $tpl->set('items', erLhcoreClassChat::getList(array('filter' => array('online_user_id' => $online_user->id),'limit' => 100,'sort' => 'id DESC'),'erLhcoreClassModelChatFile','lh_chat_file'));
    
    echo json_encode(array('result' => $tpl->fetch()));
}

exit;
?>