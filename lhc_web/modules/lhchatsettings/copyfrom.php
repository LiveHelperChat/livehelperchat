<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

if ($Params['user_parameters']['from'] == 'default') {
    
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $item = new erLhcoreClassModelChatStartSettings();
    $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Copy of Default');
    $item->department_id = erLhcoreClassModelDepartament::findOne(['sort' => 'id ASC'])->id;
    $item->data = $startData->value;
    $item->saveThis();

} elseif (is_numeric($Params['user_parameters']['from'])) {

    $itemOld = erLhcoreClassModelChatStartSettings::fetch($Params['user_parameters']['from']);

    if ($itemOld instanceof erLhcoreClassModelChatStartSettings) {
        $itemOld->id = null;
        $itemOld->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Copy of').' '.$itemOld->name;
        $itemOld->saveThis();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;


?>