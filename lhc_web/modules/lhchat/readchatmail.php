<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/printchat.tpl.php');

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    $cfgSite = erConfigClassLhConfig::getInstance();
    $secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
    $hashVerify = sha1($secretHash . $chat->hash . $chat->id);

    if ($hashVerify == $Params['user_parameters']['hash']) {
        $errors = array();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_print',array('chat' => & $chat, 'errors' => & $errors));

        if(empty($errors)) {
            erLhcoreClassChat::setTimeZoneByChat($chat);
            $tpl->set('chat',$chat);
        } else {
            $tpl->set('errors',$errors);
            $tpl->setFile('lhkernel/validation_error.tpl.php');
        }

    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
    $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>