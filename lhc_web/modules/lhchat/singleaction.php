<?php

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) ) {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/singleaction.tpl.php');
    $tpl->set('chat',$chat);
    $tpl->set('singleAction',$Params['user_parameters']['action']);
    echo $tpl->fetch();
    exit;
}

exit();
?>