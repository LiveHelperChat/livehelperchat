<?php

$tpl = new erLhcoreClassTemplate();
$ReturnMessages = array();

/**
 * Active chats
 * */
$tpl->set('right',false);
$chats = erLhcoreClassChat::getActiveChats(10);
$tpl->set('chats',$chats);
$ReturnMessages[] = array('dom_id' => '#active-chat-list', 'content' => trim($tpl->fetch( 'lhchat/lists/activechats.tpl.php')));


$tpl->set('right',true);
$ReturnMessages[] = array('dom_id' => '#right-active-chats', 'content' => trim($tpl->fetch( 'lhchat/lists/activechats.tpl.php')));


/**
 * Closed chats
 * */
$tpl->set('chats',erLhcoreClassChat::getClosedChats(10));
$ReturnMessages[] = array('dom_id' => '#closed-chat-list', 'content' => trim($tpl->fetch( 'lhchat/lists/closedchats.tpl.php')));


/**
 * Pending chats
 * */

$tpl->set('right',false);
$tpl->set('chats',erLhcoreClassChat::getPendingChats(10));
$tpl->set('transferchats',erLhcoreClassTransfer::getTransferChats());


$ReturnMessages[] = array('dom_id' => '#pending-chat-list', 'content' => trim($tpl->fetch('lhchat/lists/pendingchats.tpl.php')));


$tpl->set('right',true);
$ReturnMessages[] = array('dom_id' => '#right-pending-chats', 'content' => trim($tpl->fetch('lhchat/lists/pendingchats.tpl.php')));


$ReturnMessages[] = array('dom_id' => '#right-transfer-chats', 'content' => trim($tpl->fetch('lhchat/lists/transferedchats.tpl.php')));



$currentUser = erLhcoreClassUser::instance();    
$currentUser->updateLastVisit();


echo json_encode(array('error' => 'false', 'result' => $ReturnMessages ));
exit;
?>