<?php

$user = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['user_id']);

$currentUser = erLhcoreClassUser::instance();

if ($Params['user_parameters_unordered']['mode'] == 'check')
{
    $chat = erLhcoreClassModelChat::findOne(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT, 'sender_user_id' => $currentUser->getUserID(), 'user_id' => $user->id)));
    
    if (!($chat instanceof erLhcoreClassModelChat))
    {
        $chat = erLhcoreClassModelChat::findOne(array('filter' => array('status' => erLhcoreClassModelChat::STATUS_OPERATORS_CHAT, 'sender_user_id' => $user->id, 'user_id' => $currentUser->getUserID())));
    }
    
    // We have found previous chat, we do not need anything more
    if ($chat instanceof erLhcoreClassModelChat)
    {
        header ( 'content-type: application/json; charset=utf-8' );
        
        // Remove transfer if it exists
        $transfer = erLhcoreClassModelTransfer::findOne(array('filter' => array('transfer_to_user_id' => $currentUser->getUserID(), 'chat_id' => $chat->id)));
        
        if ($transfer instanceof erLhcoreClassModelTransfer) {
            $transfer->removeThis();
        }
        
        // Started chat
        echo erLhcoreClassChat::safe_json_encode(array('nick' => $chat->nick, 'has_chat' => true, 'chat_id' => $chat->id));
        exit;
    }
}

$tpl = erLhcoreClassTemplate::getInstance('lhchat/startchatwithoperator.tpl.php');
$tpl->set('user',$user);
$msg = new erLhcoreClassModelmsg();

if ( isset($_POST['SendMessage']) ) {

    $validationFields = array();
    $validationFields['Message'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );

    $form = new ezcInputForm( INPUT_POST, $validationFields );
    $Errors = array();

    if ( !$form->hasValidData( 'Message' ) || $form->Message == '' ) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
    } elseif ($form->hasValidData( 'Message' )) {
        $msg->msg = $form->Message;
    }

    if ($form->hasValidData( 'Message' ) && $form->Message != '' && mb_strlen($form->Message) > (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum').' '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters for a message');
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_startchatwithoperator',array('errors' => & $Errors));

    if (count($Errors) == 0) {

    	$currentUserData = $currentUser->getUserData();
    	
    	if (!($chat instanceof erLhcoreClassModelChat))
    	{
    	    $chat = new erLhcoreClassModelChat();
    	    $chat->time = time();
    	    $chat->status = erLhcoreClassModelChat::STATUS_OPERATORS_CHAT;
    	    $chat->setIP();
    	    $chat->hash = erLhcoreClassChat::generateHash();
    	    $chat->referrer = '';
    	    $chat->session_referrer = '';
    	    $chat->nick = $currentUserData->name.' '.$currentUserData->surname;
    	    $chat->user_id = $user->id; // Assign chat to receiver operator, this way he will get permission to open chat
    	    $chat->dep_id = erLhcoreClassUserDep::getDefaultUserDepartment(); // Set default department to chat creator, this way current user will get permission to open it
    	    $chat->sender_user_id = $currentUser->getUserID();
    	    
    	    $chat->saveThis();
    	}
    	
    	// Store User Message
    	$msg->chat_id = $chat->id;
    	$msg->user_id = $currentUser->getUserID();
    	$msg->time = time();
    	$msg->name_support = trim($currentUserData->name.' '.$currentUserData->surname);
    	erLhcoreClassChat::getSession()->save($msg);

    	$transfer = new erLhcoreClassModelTransfer();
    	$transfer->chat_id = $chat->id;

    	$transfer->from_dep_id = $chat->dep_id;

    	// User which is transfering
    	$transfer->transfer_user_id = $currentUser->getUserID();

    	// To what user
    	$transfer->transfer_to_user_id = $user->id;

    	erLhcoreClassTransfer::getSession()->save($transfer);
    	
    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.startchatwithoperator_started',array('chat' => & $chat, 'transfer' => & $transfer));

    	// Started chat
    	$tpl->set('started_chat', $chat);
    	
    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('msg',$msg);

echo $tpl->fetch();
exit;

?>