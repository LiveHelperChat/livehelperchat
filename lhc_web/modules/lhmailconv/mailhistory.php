<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailhistory.tpl.php');

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvConversation)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
    if (isset($mailData['mail'])) {
        $mail = $mailData['mail'];
    }
}

if ( erLhcoreClassChat::hasAccessToRead($mail) )
{
    $tpl->set('chat',$mail);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>