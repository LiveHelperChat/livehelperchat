<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = new erLhcoreClassModelUser();

        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'createuser')) {
            throw new Exception('You do not have permission to create a user. `lhuser`, `createuser` is required.');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'PUT') {

        $user = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['id']);

        if ( $_SERVER['REQUEST_METHOD'] == 'PUT' && !erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'edituser')) {
            throw new Exception('You do not have permission to edit a user. `lhuser`, `edituser` is required.');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && !erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'userlist')) {
            throw new Exception('You do not have permission to list a users. `lhuser`, `createuser` is required.');
        }

        if (!($user instanceof erLhcoreClassModelUser)) {
            throw new Exception('User could not be found!');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            erLhcoreClassRestAPIHandler::outputResponse(array
                (
                    'error' => false,
                    'result' => erLhcoreClassRestAPIUserValidator::formatAPI($user)
                )
            );
            exit;
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $user = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['id']);
        if (!($user instanceof erLhcoreClassModelUser)) {
            throw new Exception('User could not be found!');
        }

        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'deleteuser')) {
            throw new Exception('You do not have permission to delete a user. `lhuser`, `deleteuser` is required.');
        }

        $user->removeThis();

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => true));
        exit;
    }

    $groups_can_edit = erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'editusergroupall') == true ? true : erLhcoreClassGroupRole::getGroupsAccessedByUser(erLhcoreClassRestAPIHandler::getUser() );
    $userParams = array('payload_data' => $requestBody, 'show_all_pending' => 1, 'global_departament' => array(), 'groups_can_read' => array(), 'groups_can_edit' => ($groups_can_edit === true ? true : $groups_can_edit['groups']));

    $Errors = erLhcoreClassRestAPIUserValidator::validateUser($user, $userParams);

    if (count($Errors) == 0)
    {

        $db = ezcDbInstance::get();

        $db->beginTransaction();

        $user->saveThis();

        if (isset($user->departments_ids_array)) {
            if (count($user->departments_ids_array) > 0) {
                erLhcoreClassUserDep::addUserDepartaments($user->departments_ids_array, $user->id, $user, $user->departments_ids_read_array);
            } else {
                erLhcoreClassUserDep::addUserDepartaments(array(), $user->id, $user, $user->departments_ids_read_array);
            }
        }

        if (isset($user->user_groups_id)) {
            $user->setUserGroups();
        }

        if (isset($user->department_groups)) {
            erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($user, $user->department_groups);
        }

        erLhcoreClassUserDep::setHideOnlineStatus($user);

        $user->refreshThis();

        $userPhotoErrors = erLhcoreClassRestAPIUserValidator::validateOperatorPhotoPayload($user, array('payload' => $requestBody));

        if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            $user->saveThis();
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.user_created', array('userData' => & $user, 'password' => $user->password_front));

        $db->commit();

    }  else {
        throw new Exception(implode("\n",$Errors));
    }

    erLhcoreClassRestAPIHandler::outputResponse(array
        (
            'error' => false,
            'result' => erLhcoreClassRestAPIUserValidator::formatAPI($user)
        )
    );

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

