<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/previewchat.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {
    $tpl->set('keyword',isset($_GET['keyword']) ? (string)$_GET['keyword'] : '');
    $tpl->set('chat',$chat);
    $tpl->set('see_sensitive_information', $currentUser->hasAccessTo('lhchat','see_sensitive_information'));
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>