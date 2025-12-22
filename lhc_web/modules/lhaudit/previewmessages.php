<?php

$tpl = erLhcoreClassTemplate::getInstance('lhaudit/previewmessages.tpl.php');

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {
    $tpl->set('chat',$chat);
}

echo $tpl->fetch();
exit;











