<?php

$tpl = erLhcoreClassTemplate::getInstance('lhsystem/singlesetting.tpl.php');

$Params['user_parameters']['identifier'] = strip_tags($Params['user_parameters']['identifier']);

$config = erLhcoreClassModelChatConfig::fetch($Params['user_parameters']['identifier']);

if ($config->identifier == $Params['user_parameters']['identifier'])
{
    if (ezcInputForm::hasPostData() && $currentUser->hasAccessTo('lhchat','administrateconfig')) {

        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            erLhcoreClassModule::redirect();
            exit;
        }

        $config->value = isset($_POST[$config->identifier.'ValueParam']) ? $_POST[$config->identifier.'ValueParam'] : 0;
        $config->saveThis();

        // Cleanup cache to recompile templates etc.
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        $tpl->set('updated', true);
    }

    $tpl->set('action_url', erLhcoreClassDesign::baseurl('system/singlesetting') . '/' . $Params['user_parameters']['identifier']);
    $tpl->set('attribute', $Params['user_parameters']['identifier']);
    $tpl->set('boolValue', true);
}

echo $tpl->fetch();
exit;

?>