<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $userlist = erLhcoreClassModelUser::getUserList();
    
    foreach($userlist as $index => $user)
    {
        // loose password
        unset($user->password);
        
        $userlist[$index] = $user;
    }
    
    erLhcoreClassRestAPIHandler::outputResponse(array
            (
                'error' => false, 
                'result' => $userlist
            )
    );
    
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

