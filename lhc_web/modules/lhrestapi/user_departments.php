<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    // init data
    $user_id        = isset($_GET['user_id'])? intval($_GET['user_id']) : (isset($_POST['user_id']) ? intval($_POST['user_id']) : 0);

    // init user
    $user = ($user_id > 0)? erLhcoreClassModelUser::fetch($user_id) : erLhcoreClassModelUser::findOne(array('filter' => array('id' => erLhcoreClassRestAPIHandler::getUserId())));

    // check we have data
    if (! ($user instanceof erLhcoreClassModelUser))
    {
        throw new Exception('User could not be found!');
    }

    $departmentParams = array();
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($user->id);
    if ($userDepartments !== true) {
        $departmentParams['filterin']['id'] = $userDepartments;
    }

    $departmentParams['sort'] = 'sort_priority ASC, name ASC';

    $departments = erLhcoreClassModelDepartament::getList($departmentParams);

    $attrString = array('mod_start_hour','mod_start_hour','mod_end_hour','tud_start_hour',
        'tud_end_hour','wed_start_hour','wed_end_hour','thd_start_hour',
        'thd_end_hour','frd_start_hour','frd_end_hour','sad_start_hour',
        'sad_end_hour','sud_start_hour','sud_end_hour');

    foreach ($departments as & $department) {
        foreach ($attrString as $attr){
            $department->{$attr} = (string)$department->{$attr};
        }
    }

    erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => array_values($departments)));

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();
