<?php 

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/bbcodeinsert.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id'])) {
    $tpl->set('chat_id', (int)$Params['user_parameters']['chat_id']);
} else {
    $tpl->set('chat_id', null);
}

echo $tpl->fetch();
exit;