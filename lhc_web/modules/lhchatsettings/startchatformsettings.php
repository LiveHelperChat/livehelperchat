<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/startchatformsettings.tpl.php');

$startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
$data = (array)$startData->data;

if ( isset($_POST['CancelConfig']) ) {
    erLhcoreClassModule::redirect('system/configuration');
    exit;
}

if (isset($_POST['UpdateConfig']) || isset($_POST['SaveConfig']))
{    
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('chat/startchatformsettings');
        exit;
    }
    
    $Errors = erLhcoreClassAdminChatValidatorHelper::validateStartChatForm($data);

    if ( count($Errors) == 0 ) {

        $startData->value = serialize($data);
        $startData->saveThis();

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

$tpl->set('start_chat_data',$data);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.startchatformgenerator.js').'"></script>';

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Start chat form settings')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.startchatformsettings_path',array('result' => & $Result));

?>