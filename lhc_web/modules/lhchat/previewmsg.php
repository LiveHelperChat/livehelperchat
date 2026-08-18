<?php

$msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);

if (!is_object($msg)) {
    die('Message not found!');
}

$chat = erLhcoreClassModelChat::fetch($msg->chat_id);

$tpl = erLhcoreClassTemplate::getInstance('lhchat/previewmsg.tpl.php');

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {
    $tpl->set('msg',$msg->getState());
    $tpl->set('metaMessageData',$msg->meta_msg_array);
    $tpl->set('see_sensitive_information', !((int)erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value == 1) || $currentUser->hasAccessTo('lhchat','see_sensitive_information'));
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $Result['content'] =  $tpl->fetch();
    $Result['modal_header_title'] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'No permission');
    $Result['pagelayout'] = 'modal';
}

?>