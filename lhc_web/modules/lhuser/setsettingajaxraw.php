<?php

// Make sure that we support variable which is setting now
// It was possible in another portal to cheat, and overload server without this type of checking
try {
    // Start session if required only
    $currentUser = erLhcoreClassUser::instance();

    if ($currentUser->isLogged() && (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN']))) {
        throw new Exception('Invalid CSFR Token');
    }

    $settingHandler = erLhcoreClassModelUserSettingOption::fetch($Params['user_parameters']['identifier']);
    
    // Never trust user input    
    erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],(string)$_POST['value']);
    exit;
    
} catch (Exception $e){

}

exit;
?>