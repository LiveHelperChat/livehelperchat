<?php

// Make sure that we support variable which is setting now
// It was possible in another portal to cheat, and overload server without this type of checking
try {
	// Start session if required only
	$currentUser = erLhcoreClassUser::instance();

	if ($Params['user_parameters']['identifier'] != 'online_connected') {
        $settingHandler = erLhcoreClassModelUserSettingOption::fetch($Params['user_parameters']['identifier']);
    }

    // Never trust user input    
    if (!isset($Params['user_parameters_unordered']['indifferent'])){
    	erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],$Params['user_parameters']['value'] == 1 ? 1 : 0);    
    } else {  
    	erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],(string)strip_tags(rawurldecode($Params['user_parameters']['value'])));
    }
    exit;

} catch (Exception $e) {

}

exit;
?>