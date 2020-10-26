<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $rawAttributes = false;

    // To use this we need to change how form components works and check do we have post variable in general
    // This part is used by mobile App
    if (isset($_POST['post_body'])) {

        $requestBody = json_decode($_POST['post_body'],true);
        if (isset($_POST['request_method'])) {
            $_SERVER['REQUEST_METHOD'] = $_POST['request_method'];
        }

        if (isset($_POST['raw_attr'])) {
            $rawAttributes = true;
        }

    } else {
        $requestBody = json_decode(file_get_contents('php://input'),true);

        if (isset($requestBody['raw_attr']) && $requestBody['raw_attr'] == true) {
            $rawAttributes = true;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $dep = new erLhcoreClassModelDepartament();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $dep = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);
        if (!($dep instanceof erLhcoreClassModelDepartament)) {
            throw new Exception('Department could not be found!');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $dep = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);
        if (!($dep instanceof erLhcoreClassModelDepartament)) {
            throw new Exception('Department could not be found!');
        }

        if ($dep->can_delete = true) {
            $dep->removeThis();
        } else {
            throw new Exception('You can not delete department because he has a chats!');
        }

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false,'result' => true));
        exit;
    }

    if ($rawAttributes == true){
        $Errors = [];
        foreach ($requestBody as $attr => $value) {
            if ($attr != 'id') {
                $dep->{$attr} = $value;
            }
        }
    } else {
        $Errors = erLhcoreClassDepartament::validateDepartment($dep, array('payload_data' => $requestBody));
    }

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

