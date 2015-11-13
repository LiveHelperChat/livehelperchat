<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/autologinconfig.tpl.php');

$autologinData = erLhcoreClassModelChatConfig::fetch('autologin_data');
$data = (array)$autologinData->data;

if ( isset($_POST['StoreAutologinSettings']) ) {
    $definition = array(
        'secret_hash' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'enabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )           
    );
    
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/autologinconfig');
        exit;
    }
    
    $Errors = array();
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    try {    
        if ( $form->hasValidData( 'secret_hash' ) && strlen($form->secret_hash) >= 10 ) {
            $data['secret_hash'] = $form->secret_hash;
        } else {
            throw new Exception('Please enter secret hash');
        }
        
        if ( $form->hasValidData( 'enabled' ) && $form->enabled == true ) {
            $data['enabled'] = 1;
        } else {
            $data['enabled'] = 0;
        }
                   
        $autologinData->value = serialize($data);
        $autologinData->saveThis();
        
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();
        
        $tpl->set('updated','done');
        
    } catch (Exception $e) {
        $tpl->set('errors',array($e->getMessage()));
    }
}

$tpl->set('autologin_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Auto login settings')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.autologinconfig_path', array('result' => & $Result));

?>