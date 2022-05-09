<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/icondetailed.tpl.php');

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);
$column = erLhAbstractModelChatColumn::fetch($Params['user_parameters']['column_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {
    $column->column_name = erLhcoreClassGenericBotWorkflow::translateMessage($column->column_name, array('chat' => $chat, 'args' => ['chat' => $chat]));
    $column->popup_content = erLhcoreClassGenericBotWorkflow::translateMessage($column->popup_content, array('chat' => $chat, 'args' => ['chat' => $chat]));
    $tpl->set('column',$column);
    $tpl->set('chat',$chat);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>