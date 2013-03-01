<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {
    
    list($chatID,$hash) = explode('_',$hashSession);
    
    // Redirect user
    erLhcoreClassModule::redirect('chat/chatwidgetchat/' . $chatID . '/' . $hash);
    exit;
}

$tpl = new erLhcoreClassTemplate( 'lhchat/chatwidget.tpl.php');
$tpl->set('referer','');

// Start chat field options
$startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
$startDataFields = (array)$startData->data;

$inputData = new stdClass();
$inputData->username = '';
$inputData->question = '';
$inputData->email = '';
$inputData->departament_id = 0;
$inputData->validate_start_chat = false;

$chat = new erLhcoreClassModelChat();

if (isset($_POST['StartChat']))
{
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
       if (isset($startDataFields['message_visible_in_page_widget']) && $startDataFields['message_visible_in_page_widget'] == true) {           
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

       // Store hash if user reloads page etc, we show widget
       CSCacheAPC::getMem()->setSession('chat_hash_widget',$chat->id.'_'.$chat->hash);

       // Redirect user
       erLhcoreClassModule::redirect('chat/chatwidgetchat/' . $chat->id . '/' . $chat->hash);
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
$Result['pagelayout'] = 'widget';

?>