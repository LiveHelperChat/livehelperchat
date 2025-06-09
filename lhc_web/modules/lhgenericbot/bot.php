<?php

if ($Params['user_parameters_unordered']['type'] == 'chart') {
    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/bot_chart.tpl.php');
    $bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);
    $tpl->set('bot',$bot);
    echo $tpl->fetch();
    exit;
} else {
    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/bot.tpl.php');
}


$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$tpl->set('bot',$bot);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Bot constructor')),
    array('title' => $bot->name));

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/react/build/all.js').'"></script>';

?>