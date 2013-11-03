<?php

$activated = 'false';
$result = 'false';
$ott = '';

$tpl = erLhcoreClassTemplate::getInstance('lhchat/checkchatstatus.tpl.php');

try {
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

    if ($chat->hash == $Params['user_parameters']['hash']) {
	    if ( erLhcoreClassChat::isOnline($chat->dep_id) ) {
	         $tpl->set('is_online',true);
	    } else {
	         $tpl->set('is_online',false);
	    }

	    if ( $chat->chat_initiator == erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE ) {
	         $tpl->set('is_proactive_based',true);
	    } else {
	         $tpl->set('is_proactive_based',false);
	    }

	    if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
	       $activated = 'true';
	       $tpl->set('is_activated',true);
	       $ott = ($chat->user !== false) ? $chat->user->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...');
	    } else {
	       $tpl->set('is_activated',false);
	    }

	    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
	    	$activated = 'true';
	    	$tpl->set('is_closed',true);
	    } else {
	    	$tpl->set('is_closed',false);
	    }

	    $tpl->set('chat', $chat);
    }


} catch (Exception $e) {
    exit;
}

echo json_encode(array('error' => 'false','ott' => $ott, 'result' => $tpl->fetch(),'activated' => $activated));
exit;
?>