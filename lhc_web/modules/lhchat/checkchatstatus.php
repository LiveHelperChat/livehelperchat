<?php

$activated = 'false';
$result = 'false';

$tpl = erLhcoreClassTemplate::getInstance('lhchat/checkchatstatus.tpl.php');

try {
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::isOnline($chat->dep_id) ) {
         $tpl->set('is_online',true);
    } else {
         $tpl->set('is_online',false);
    }

    if (erLhcoreClassChat::isChatActive($Params['user_parameters']['chat_id'],$Params['user_parameters']['hash'])) {
       $activated = 'true';
       $tpl->set('is_activated',true);
    } else {
       $tpl->set('is_activated',false);
    }

    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
    	$activated = 'true';
    	$tpl->set('is_closed',true);
    } else {
    	$tpl->set('is_closed',false);
    }


} catch (Exception $e) {
    exit;
}

echo json_encode(array('error' => 'false', 'result' => $tpl->fetch(),'activated' => $activated));
exit;
?>