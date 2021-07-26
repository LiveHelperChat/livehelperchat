<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/attatchtemplate.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>