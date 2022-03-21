<?php

// Make sure that we support variable which is setting now
// It was possible in another portal to cheat, and overload server without this type of checking
try {

    // Start session if required only
    $currentUser = erLhcoreClassUser::instance();

    if ($currentUser->isLogged() && (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN']))) {
        throw new Exception('Invalid CSFR Token');
    }

    if (!in_array($Params['user_parameters']['identifier'],[
        'online_connected',
        'oattrf_key_1',
        'oattrf_val_1',
        'oattrf_key_2',
        'oattrf_val_2',
        'oattrf_key_3',
        'oattrf_val_3',
        'oattrf_key_4',
        'oattrf_val_4',
        'oattrf_key_5',
        'oattrf_val_5',
    ])) {
        $settingHandler = erLhcoreClassModelUserSettingOption::fetch($Params['user_parameters']['identifier']);
    }

    // Never trust user input    
    if (!isset($Params['user_parameters_unordered']['indifferent'])){
    	erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],$Params['user_parameters']['value'] == 1 ? 1 : 0);
    } else {
        $val = (string)strip_tags(rawurldecode($Params['user_parameters']['value']));
    	erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],$val == '__empty__' ? '' : $val);
    }
    exit;

} catch (Exception $e) {
    echo "error";
}

exit;
?>