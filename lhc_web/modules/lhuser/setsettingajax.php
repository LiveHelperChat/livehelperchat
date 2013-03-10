<?php

// Make sure that we support variable which is setting now
// It was possible in another portal to cheat, and overload server without this type of checking
try {
    
    $settingHandler = erLhcoreClassModelUserSettingOption::fetch($Params['user_parameters']['identifier']);
        
    // Never trust user input    
    erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],$Params['user_parameters']['value'] == 1 ? 1 : 0);    
    exit;
    
} catch (Exception $e){
	print_r($e);
}

exit;
?>