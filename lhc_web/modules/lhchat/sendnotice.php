<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/sendnotice.tpl.php');

$visitor = erLhcoreClassModelChatOnlineUser::fetch((int)$Params['user_parameters']['online_id']);

$tpl->set('visitor',$visitor);

if ( isset($_POST['SendMessage']) ) {
    
    $validationFields = array();
    $validationFields['Message'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresEmail'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresUsername'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['RequiresPhone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    
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

    if ($form->hasValidData( 'RequiresEmail' ) && $form->RequiresEmail == true) {
        $visitor->requires_email = 1;
    } else {
    	$visitor->requires_email = 0;
    }

    if ($form->hasValidData( 'RequiresUsername' ) && $form->RequiresUsername == true) {
        $visitor->requires_username = 1;
    } else {
    	$visitor->requires_username = 0;
    }

    if ($form->hasValidData( 'RequiresPhone' ) && $form->RequiresPhone == true) {
        $visitor->requires_phone = 1;
    } else {
    	$visitor->requires_phone = 0;
    }
   
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.sendnotice', array('errors' => & $Errors));
            
    if (count($Errors) == 0) { 
               
        $currentUser = erLhcoreClassUser::instance();   
        $visitor->message_seen = 0;
        $visitor->invitation_id = -1;
        $visitor->show_on_mobile = 1;
        $visitor->operator_user_id = $currentUser->getUserID();
        $visitor->saveThis();
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_send_invitation', array('ou' => & $visitor));
        
        $tpl->set('message_saved',true);    
    } else {        
        $tpl->set('errors',$Errors);
    } 
}

echo $tpl->fetch();
exit;

?>