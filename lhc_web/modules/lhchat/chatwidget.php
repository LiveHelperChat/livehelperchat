<?php

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {
    
    list($chatID,$hash) = explode('_',$hashSession);
    
    // Redirect user
    erLhcoreClassModule::redirect('chat/chatwidgetchat/' . $chatID . '/' . $hash);
    exit;
}

$tpl = new erLhcoreClassTemplate( 'lhchat/chatwidget.tpl.php');
$tpl->set('referer','');

$inputData = new stdClass();
$inputData->username = '';
$inputData->Question = '';
$inputData->email = '';
$inputData->departament_id = 0;

$chat = new erLhcoreClassModelChat();

if (isset($_POST['StartChat']))
{
   $definition = array(
        'Username' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Question' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
        ),
        'DepartamentID' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
        ),
    );
  
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Username' ) || $form->Username == '' )
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
    }
    
    if ( !$form->hasValidData( 'Question' ) || $form->Question == '' )
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
    }
    
    if ($form->hasValidData( 'Username' ) && $form->Username != '' && strlen($form->Username) > 50)
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 50 characters');
    }
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Wrong email');
    }
    
    $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    
    if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $ip))) > 0) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You do not have permission to chat! Please contact site owner.');
    }
    
    $departments = erLhcoreClassModelDepartament::getList();
    
    $ids = array_keys($departments);
        
    if ($form->hasValidData( 'DepartamentID' ) && in_array($form->DepartamentID,$ids)) {
        $chat->dep_id = $form->DepartamentID;
    } else {
        $id = array_shift($ids);
        $chat->dep_id = $id;
    }
    
    if (count($Errors) == 0)
    {       
       $chat->nick = $form->Username;
       $chat->email = $form->Email;
       $chat->time = time();
       $chat->status = 0;       
       $chat->setIP();
       $chat->hash = erLhcoreClassChat::generateHash();
       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
       
       erLhcoreClassModelChat::detectLocation($chat);
       
       // Store chat
       erLhcoreClassChat::getSession()->save($chat);
       
       // Store question as message
       $msg = new erLhcoreClassModelmsg();
       $msg->msg = trim($form->Question);
       $msg->status = 0;
       $msg->chat_id = $chat->id;
       $msg->user_id = 0;
       $msg->time = time();
     
       erLhcoreClassChat::getSession()->save($msg);

       // Store hash if user reloads page etc, we show widget
       CSCacheAPC::getMem()->setSession('chat_hash_widget',$chat->id.'_'.$chat->hash);
       
       // Redirect user
       erLhcoreClassModule::redirect('chat/chatwidgetchat/' . $chat->id . '/' . $chat->hash);
       exit;
    } else {        
        $tpl->set('errors',$Errors);
    }  
}

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