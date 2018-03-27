<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/bot.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => 'Generic Bot'));

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/react/build/all.js').'"></script>';

?>