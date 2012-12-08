<?php

$Activated = 'false';
$result = 'false';

// FIXME, check is online by user chosen departament
if (erLhcoreClassChat::isOnline()) {
    
    if (erLhcoreClassChat::isChatActive($Params['user_parameters']['chat_id'],$Params['user_parameters']['hash']))
    {
       $Activated = 'true';
       $result = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Support staff member have joined this chat');       
    } else {
       $result = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Pending support staff member to join, you can write your questions, as soon support staff member confirm this chat, he will get your messages');
    }
    
} else {
   $result = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','At this moment there are no logged members, but you can leave your messages.'); 
}

echo json_encode(array('error' => 'false', 'result' => $result,'activated' => $Activated));
exit;
?>