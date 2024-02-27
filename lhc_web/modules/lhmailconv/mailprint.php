<?php

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvMessage)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
    if (isset($mailData['mail'])) {
        $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetchAndLock($Params['user_parameters']['id']);
    }
}

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailprint.tpl.php');
$tpl->set('chat',$mail);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>