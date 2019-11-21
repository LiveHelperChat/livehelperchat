<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $dep = new erLhcoreClassModelDepartament();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $dep = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);
        if (!($dep instanceof erLhcoreClassModelDepartament)) {
            throw new Exception('Bot could not be found!');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $dep = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);
        if (!($dep instanceof erLhcoreClassModelDepartament)) {
            throw new Exception('Bot could not be found!');
        }

        if ($dep->can_delete = true) {
            $dep->removeThis();
        } else {
            throw new Exception('You can not delete department because he has a chats!');
        }

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false,'result' => true));
        exit;
    }

    $Errors = erLhcoreClassDepartament::validateDepartment($dep, array('payload_data' => $requestBody));

    if (count($Errors) == 0)
    {
        $dep->saveThis();

        $DepartamentCustomWorkHours = erLhcoreClassModelDepartamentCustomWorkHours::getList(array('filter' => array('dep_id' => $dep->id),'sort' => 'date_from ASC'));

        $DepartamentCustomWorkHours = erLhcoreClassDepartament::validateDepartmentCustomWorkHours($dep, $DepartamentCustomWorkHours);

        erLhcoreClassDepartament::validateDepartmentProducts($dep);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('department.modified',array('department' => $dep, 'payload_data' => $requestBody));

    }  else {
        throw new Exception(implode("\n",$Errors));
    }

    erLhcoreClassRestAPIHandler::outputResponse(array
        (
            'error' => false,
            'result' => $dep
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

