<?php

$tpl = erLhcoreClassTemplate::getInstance('lhsystem/singlesetting.tpl.php');

$Params['user_parameters']['identifier'] = strip_tags($Params['user_parameters']['identifier']);

// Only these settings may be changed through this endpoint, and only in the listed
// format. The single-setting modal renders a boolean toggle, so a legitimate request
// can only ever submit "1" (or omit the field, meaning "0") - any other value is
// rejected here instead of being stored verbatim.
$allowedSettings = array(
    'guardrails_enabled'  => 'bool',
    'ignore_user_status'  => 'bool',
);

if (!array_key_exists($Params['user_parameters']['identifier'], $allowedSettings)) {
    erLhcoreClassModule::redirect();
    exit;
}

$config = erLhcoreClassModelChatConfig::fetch($Params['user_parameters']['identifier']);

$tpl->set('attribute', $Params['user_parameters']['identifier']);

if ($config->identifier == $Params['user_parameters']['identifier'])
{
    if (ezcInputForm::hasPostData()) {

        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            erLhcoreClassModule::redirect();
            exit;
        }

        $value = isset($_POST[$config->identifier.'ValueParam']) ? $_POST[$config->identifier.'ValueParam'] : 0;

        // Enforce the format declared in $allowedSettings instead of trusting the request.
        switch ($allowedSettings[$Params['user_parameters']['identifier']]) {
            case 'bool':
                $value = ($value === '1' || $value === 1) ? '1' : '0';
                break;
        }

        $config->value = $value;
        $config->saveThis();

        // Cleanup cache to recompile templates etc.
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        $tpl->set('updated', true);
    }

    $tpl->set('action_url', erLhcoreClassDesign::baseurl('system/singlesetting') . '/' . $Params['user_parameters']['identifier']);
    $tpl->set('boolValue', true);
}


echo $tpl->fetch();
exit;

?>