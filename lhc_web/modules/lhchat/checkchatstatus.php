<?php

$Activated = 'false';
$result = 'false';

$tpl = erLhcoreClassTemplate::getInstance('lhchat/checkchatstatus.tpl.php');

// FIXME, check is online by user chosen departament
if (erLhcoreClassChat::isOnline()) {
    
    $tpl->set('is_online',true);
    
    if (erLhcoreClassChat::isChatActive($Params['user_parameters']['chat_id'],$Params['user_parameters']['hash'])) {
       $Activated = 'true';
       $tpl->set('is_activated',true);
    } else {
       $tpl->set('is_activated',false); 
    }
    
} else {
   $tpl->set('is_online',false);
}

echo json_encode(array('error' => 'false', 'result' => $tpl->fetch(),'activated' => $Activated));
exit;
?>