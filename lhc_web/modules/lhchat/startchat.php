<?php

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {
    
    list($chatID,$hash) = explode('_',$hashSession);

    // Remove chat from chat widget, from now user will be communicating using popup window
    CSCacheAPC::getMem()->setSession('chat_hash_widget',false);
    
    // Redirect user
    erLhcoreClassModule::redirect('chat/chat/' . $chatID . '/' . $hash);
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/startchat.tpl.php');
$tpl->set('referer','');

// Start chat field options
$startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
$startDataFields = (array)$startData->data;

// Input fields holder
$inputData = new stdClass();
$inputData->username = '';
$inputData->question = '';
$inputData->email = '';
$inputData->phone = '';
$inputData->departament_id = 0;
$inputData->validate_start_chat = true;

$chat = new erLhcoreClassModelChat();

if (isset($_POST['StartChat'])) {
   // Validate post data
   $Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat);

   if (count($Errors) == 0)
   {   
       $chat->time = time();
       $chat->status = 0;       
       $chat->setIP();
       $chat->hash = erLhcoreClassChat::generateHash();
       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
       
       if ( empty($chat->nick) ) {
           $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
       }
       
       erLhcoreClassModelChat::detectLocation($chat);
       
       // Store chat
       erLhcoreClassChat::getSession()->save($chat);
       
       // Assign chat to user
       if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
            // To track online users
            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest();
            
            if ($userInstance !== false) {
                $userInstance->chat_id = $chat->id;
                $userInstance->saveThis();
            }            
       }
        
       // Store message if required
       if (isset($startDataFields['message_visible_in_popup']) && $startDataFields['message_visible_in_popup'] == true) {           
           if ( $inputData->question != '' ) {           
               // Store question as message
               $msg = new erLhcoreClassModelmsg();
               $msg->msg = trim($inputData->question);
               $msg->status = 0;
               $msg->chat_id = $chat->id;
               $msg->user_id = 0;
               $msg->time = time();

               erLhcoreClassChat::getSession()->save($msg);
           }       
       }
              
       // Redirect user
       erLhcoreClassModule::redirect('chat/chat/' . $chat->id . '/' . $chat->hash);
       exit;
    } else {        
        $tpl->set('errors',$Errors);
    }  
}

$tpl->set('start_data_fields',$startDataFields);

$tpl->set('input_data',$inputData);

if (isset($_GET['URLReferer']))
{
    $tpl->set('referer',$_GET['URLReferer']);
}

if (isset($_POST['URLRefer']))
{
    $tpl->set('referer',$_POST['URLRefer']);
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat';

$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill form to start chat')))

?>