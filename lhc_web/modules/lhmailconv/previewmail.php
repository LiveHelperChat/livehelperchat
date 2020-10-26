<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/previewmail.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if ( erLhcoreClassChat::hasAccessToRead($mail) )
{
    $tpl->set('chat',$mail);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>