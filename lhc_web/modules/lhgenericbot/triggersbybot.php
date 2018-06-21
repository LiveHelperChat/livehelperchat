<?php

$bot =  erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

if ($bot instanceof erLhcoreClassModelGenericBotBot){
    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/triggersbybot.tpl.php');
    $tpl->set('bot',$bot);
    $tpl->set('trigger_id',(int)$Params['user_parameters']['trigger_id']);
    $tpl->set('preview',(int)$Params['user_parameters_unordered']['preview'] == 1);
    echo $tpl->fetch();
}

exit;
?>