<?php

$bot =  erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

if ($bot instanceof erLhcoreClassModelGenericBotBot){
    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/triggersbybot.tpl.php');
    $tpl->set('bot',$bot);
    $tpl->set('trigger_id',(int)$Params['user_parameters']['trigger_id']);
    echo $tpl->fetch();
}

exit;
?>