<?php

// Make sure that we support variable which is setting now
// It was possible in another portal to cheat, and overload server without this type of checking
try {
	// Start session if required only
	$currentUser = erLhcoreClassUser::instance();
	
    $settingHandler = erLhcoreClassModelUserSettingOption::fetch($Params['user_parameters']['identifier']);
    
    $supportedValue = false;
    
    foreach ($settingHandler->options as $option)
    {
        if ($option->{$settingHandler->attribute} == $Params['user_parameters']['value']) $supportedValue = true;
    }
            
    // Never trust user input
    if ($supportedValue === true) {
        erLhcoreClassModelUserSetting::setSetting($Params['user_parameters']['identifier'],$Params['user_parameters']['value']);    
    }    
        
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
    
} catch (Exception $e){
   
}

exit;
?>