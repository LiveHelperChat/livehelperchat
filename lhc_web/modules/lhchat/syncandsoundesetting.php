<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/syncamdsoundsettings.tpl.php');

if ( isset($_POST['CancelConfig']) ) {        
    erLhcoreClassModule::redirect('system/configuration');
    exit;
}

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings');
$data = (array)$soundData->data;

if (isset($_POST['UpdateConfig']) || isset($_POST['SaveConfig']))
{
    
    $definition = array(
        'OnlineTimeout' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'float'
        ),
        'SyncBackOffice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'float'
        ),    
        'SyncForUserMessagesEvery' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'float'
        ),
        'SyncForOperatorMessagesEvery' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'float'
        ),
        'SyncForUserMessagesEveryPolling' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'float'
        ),      
        'SoundNotificationRepeat' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
        'SoundNotificationDelay' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
        'ServerConnectionTimeout' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
        'PlayOnRequest' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'ShowAlertMessageBackOffice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'PlayOnMessageBackOffice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),  
        'PlayOnMessageFrontOffice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'EnableLongPolling' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'ShowBrowserNotificationMessage' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( $form->hasValidData( 'PlayOnRequest' ) && $form->PlayOnRequest == true ) {
        $data['new_chat_sound_enabled'] = true;
    } else {
        $data['new_chat_sound_enabled'] = false;
    }
    
    if ( $form->hasValidData( 'EnableLongPolling' ) && $form->EnableLongPolling == true ) {
        $data['long_polling_enabled'] = true;
    } else {
        $data['long_polling_enabled'] = false;
    }
    
    if ( $form->hasValidData( 'ShowBrowserNotificationMessage' ) && $form->ShowBrowserNotificationMessage == true ) {
        $data['browser_notification_message'] = true;
    } else {
        $data['browser_notification_message'] = false;
    }
        
    if ( $form->hasValidData( 'PlayOnMessageBackOffice' ) && $form->PlayOnMessageBackOffice == true ) {
        $data['new_message_sound_admin_enabled'] = true;
    } else {
        $data['new_message_sound_admin_enabled'] = false;
    }
    
    if ( $form->hasValidData( 'PlayOnMessageFrontOffice' ) && $form->PlayOnMessageFrontOffice == true ) {
        $data['new_message_sound_user_enabled'] = true;
    } else {  
        $data['new_message_sound_user_enabled'] = false;
    }
    
    if ( $form->hasValidData( 'ShowAlertMessageBackOffice' ) && $form->ShowAlertMessageBackOffice == true ) {
        $data['show_alert'] = true;
    } else {
        $data['show_alert'] = false;
    }
    
    if ( $form->hasValidData( 'OnlineTimeout' )  ) {
        $data['online_timeout'] = $form->OnlineTimeout;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid online timeout value!');
    }
    
    if ( $form->hasValidData( 'ServerConnectionTimeout' )  ) {
        $data['connection_timeout'] = $form->ServerConnectionTimeout;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid server connection timeout value!');
    }

    if ( $form->hasValidData( 'SyncForOperatorMessagesEvery' )  ) {
        $data['check_for_operator_msg'] = $form->SyncForOperatorMessagesEvery;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid operator message timeout value!');
    }
        
    if ( $form->hasValidData( 'SyncForUserMessagesEveryPolling' )  ) {
        $data['polling_chat_message_sinterval'] = $form->SyncForUserMessagesEveryPolling;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid new messages polling sync interval!');
    }
    
    if ( $form->hasValidData( 'SyncBackOffice' )  ) {
        $data['back_office_sinterval'] = $form->SyncBackOffice;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid back office sync interval!');
    }
    
    if ( $form->hasValidData( 'SyncForUserMessagesEvery' )  ) {
        $data['chat_message_sinterval'] = $form->SyncForUserMessagesEvery;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid new messages sync interval!');
    }
        
    if ( $form->hasValidData( 'SoundNotificationRepeat' )  ) {
        $data['repeat_sound'] = $form->SoundNotificationRepeat;
    }
    
    if ( $form->hasValidData( 'SoundNotificationDelay' )  ) {
        $data['repeat_sound_delay'] = $form->SoundNotificationDelay;
    }
    
    if ( count($Errors) == 0 ) {
    	
    	$soundData->value = serialize($data);
    	$soundData->saveThis();
    	    	
        $tpl->set('updated',true);
        
        // Cleanup cache to recompile templates etc.
    	$CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();
               
        if ( isset($_POST['SaveConfig']) ) {        
            erLhcoreClassModule::redirect('system/configuration');
            exit;
        }
        
        
    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('sound_data',$data);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Synchronization and sound settings')));

?>