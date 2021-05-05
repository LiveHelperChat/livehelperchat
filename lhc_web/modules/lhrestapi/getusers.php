<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'use_override' => true));

    if (isset($_GET['group_ids'])) {
        $idDep = explode(',',$_GET['group_ids']);
        erLhcoreClassChat::validateFilterIn($idDep);
        if (!empty($idDep)){
            $filterParams['input']->group_ids = $idDep;
        }
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams, 'lh_users', 'id');

    $userlist = erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'], array('offset' => 0, 'limit' => false)));

    foreach($userlist as $index => $user)
    {
        // loose password
        unset($user->password);
        
        $userlist[$index] = $user;
    }
    
    erLhcoreClassRestAPIHandler::outputResponse(array
            (
                'error' => false, 
                'result' => array_values($userlist)
            )
    );
    
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

