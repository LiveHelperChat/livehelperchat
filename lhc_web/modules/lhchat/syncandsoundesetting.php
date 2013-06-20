<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/syncamdsoundsettings.tpl.php');

if ( isset($_POST['CancelConfig']) ) {        
    erLhcoreClassModule::redirect('system/configuration');
    exit;
}

$settingsInstance = erConfigClassLhConfig::getInstance();

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
        'PlayOnRequest' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'PlayOnMessageBackOffice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),  
        'PlayOnMessageFrontOffice' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( $form->hasValidData( 'PlayOnRequest' ) && $form->PlayOnRequest == true ) {
        $settingsInstance->setSetting('chat','new_chat_sound_enabled',true);
    } else {
        $settingsInstance->setSetting('chat','new_chat_sound_enabled',false);
    }
    
    if ( $form->hasValidData( 'PlayOnMessageBackOffice' ) && $form->PlayOnMessageBackOffice == true ) {
        $settingsInstance->setSetting('chat','new_message_sound_admin_enabled',true);
    } else {
        $settingsInstance->setSetting('chat','new_message_sound_admin_enabled',false);
    }
    
    if ( $form->hasValidData( 'PlayOnMessageFrontOffice' ) && $form->PlayOnMessageFrontOffice == true ) {
        $settingsInstance->setSetting('chat','new_message_sound_user_enabled',true);
    } else {
        $settingsInstance->setSetting('chat','new_message_sound_user_enabled',false);
    }
    
    if ( $form->hasValidData( 'OnlineTimeout' )  ) {
        $settingsInstance->setSetting('chat','online_timeout',$form->OnlineTimeout);
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid online timeout value!');
    }
    
    if ( $form->hasValidData( 'SyncForOperatorMessagesEvery' )  ) {
        $settingsInstance->setSetting('chat','check_for_operator_msg',$form->SyncForOperatorMessagesEvery);
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid operator message timeout value!');
    }
    
    if ( $form->hasValidData( 'SyncBackOffice' )  ) {
        $settingsInstance->setSetting('chat','back_office_sinterval',$form->SyncBackOffice);
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid back office sync interval!');
    }
    
    if ( $form->hasValidData( 'SyncForUserMessagesEvery' )  ) {
        $settingsInstance->setSetting('chat','chat_message_sinterval',$form->SyncForUserMessagesEvery);
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Please enter a valid new messages sync interval!');
    }
    
    if ( count($Errors) == 0 ) {
        
        $tpl->set('updated',true);
        
        // Save settings
        $settingsInstance->save();
        
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

$tpl->set('settings_instance',$settingsInstance);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncandsoundesetting','Synchronization and sound settings')));

?>