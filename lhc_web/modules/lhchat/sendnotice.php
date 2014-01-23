<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/sendnotice.tpl.php');

$visitor = erLhcoreClassModelChatOnlineUser::fetch((int)$Params['user_parameters']['online_id']);

$tpl->set('visitor',$visitor);

if ( isset($_POST['SendMessage']) ) {
    
    $validationFields = array();
    $validationFields['Message'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    
    $form = new ezcInputForm( INPUT_POST, $validationFields );    
    $Errors = array();
    
    if ( !$form->hasValidData( 'Message' ) || $form->Message == '' ) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
    } elseif ($form->hasValidData( 'Message' )) {
        $visitor->operator_message = $form->Message;
    }

    if ($form->hasValidData( 'Message' ) && $form->Message != '' && mb_strlen($form->Message) > (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum').' '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters for a message');
    }
    
    if (count($Errors) == 0) { 
               
        $currentUser = erLhcoreClassUser::instance();   
        $visitor->message_seen = 0;
        $visitor->invitation_id = -1;
        $visitor->operator_user_id = $currentUser->getUserID();
        $visitor->saveThis();
        
        $tpl->set('message_saved',true);    
    } else {        
        $tpl->set('errors',$Errors);
    } 
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';
?>