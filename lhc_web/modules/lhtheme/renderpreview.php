<?php

$trigger =  erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);

if ($trigger instanceof erLhcoreClassModelGenericBotTrigger){
    $tpl = erLhcoreClassTemplate::getInstance('lhtheme/renderpreview.tpl.php');
    $tpl->set('trigger',$trigger);
    echo $tpl->fetch();
}

exit;
?>