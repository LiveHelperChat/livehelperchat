<?php

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if (!erLhcoreClassChat::hasAccessToRead($chat)) {
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/error_modal.tpl.php');
    $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat', 'No permission to access this chat.'));
    print $tpl->fetch();
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhchat/transferchat.tpl.php');
$tpl->set('chat',$chat);
$currentUser = erLhcoreClassUser::instance();
$tpl->set('user_id',$currentUser->getUserID());

print $tpl->fetch();
exit;

?>