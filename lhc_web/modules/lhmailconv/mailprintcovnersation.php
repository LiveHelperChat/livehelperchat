<?php

$mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailprintconversation.tpl.php');
$tpl->set('chat',$mail);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>