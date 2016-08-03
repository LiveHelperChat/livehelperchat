<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'nick' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

$partsReturn = array();
$partsReturn['or'] = '';
$partsReturn['ur'] = '';
$partsReturn['op'] = '';
$sender = '';
$error = 'f';

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
    $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($Chat->hash == $Params['user_parameters']['hash'])
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($form->msg);
        $msg->chat_id = $Params['user_parameters']['chat_id'];
        $msg->user_id = 0;
        $sender = $_SESSION['lhc_chatbox_nick'] = $msg->name_support = $form->nick;
        $msg->time = time();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatbox.before_msg_user_saved',array('msg' => & $msg,'chat' => & $Chat));

        erLhcoreClassChat::getSession()->save($msg);

        // Set last message ID
        if ($Chat->last_msg_id < $msg->id) {
        	$Chat->last_msg_id = $msg->id;
        }

        // Delete legacy messages, propability 1 of 100
        if (1 == mt_rand(1, 100)) {
        	erLhcoreClassChatbox::cleanupChatbox($Chat);
        }

        $Chat->last_user_msg_time = $msg->time = time();
        $Chat->has_unread_messages = 1;
        $Chat->updateThis();
       
        if ($Params['user_parameters_unordered']['render'] == 'true') {        	
        	$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/render.tpl.php');
        	$tpl->set('msg',$msg);
        	$tpl->set('chat',$Chat);      
        	$content = $tpl->fetch();
        	$parts = explode('{{SPLITTER}}', $content);
        	$partsReturn['or'] = $parts[0];
        	$partsReturn['ur'] = $parts[1];
        }

        // Just increase cache version upon message ad
        CSCacheAPC::getMem()->increaseCacheVersion('chatbox_'.erLhcoreClassChatbox::getIdentifierByChatId($Chat->id));
        
        echo json_encode(array('error' => $error,'id' => $msg->id,'or' => $partsReturn['or'],'ur' => $partsReturn['ur'],'sender' => $sender));
        exit;
    }
} else {
	$error = 't';
	$partsReturn['or'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
	echo json_encode(array('error' => $error,'or' => $partsReturn['or']));
	exit;
}



?>