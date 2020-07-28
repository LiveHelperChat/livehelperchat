<?php

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailprint.tpl.php');
$tpl->set('chat',$mail);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>