<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/bot.tpl.php');

$bot =  erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$tpl->set('bot',$bot);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => 'Generic Bot'));

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/react/build/all.js').'"></script>';

?>