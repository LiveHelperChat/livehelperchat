<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try 
{
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $userlist = erLhcoreClassModelUser::getUserList();
    
    foreach($userlist as $index => $user)
    {
        // loose password
        unset($user->password);
        
        $userlist[$index] = $user;
    } // end of foreach($userlist as $index => $user)
    
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

