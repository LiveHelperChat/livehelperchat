<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $restAPI = array('ignore_captcha' => true, 'ignore_geo' => true, 'collect_all' => true);

        include "modules/lhwidgetrestapi/submitonline.php";

        if ($outputResponse['success'] === false) {
            $errors = $Errors;
            throw new Exception(implode("\n",$outputResponse['errors']));
        }


    } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        foreach ($requestBody as $attr => $value) {
            if ($attr != 'id') { // we never update ID
                $chat->{$attr} = $value;
            }
        }

        $chat->saveThis();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }
        $chat->removeThis();

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => true));
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        if (isset($_GET['hash']) && $chat->hash != $_GET['hash']) {
            throw new Exception('Invalid hash');
        }

        if (isset($_GET['department_groups']) && $_GET['department_groups'] == 'true') {
            $chat->department_groups = array();
            foreach (erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_id' => $chat->dep_id))) as $depGroup) {
                $chat->department_groups[] = $depGroup->dep_group_id;
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('api.fetchchat', array('chat' => & $chat));

        $attributes = isset($_GET['attr']) ? explode(',',str_replace(' ','',$_GET['attr'])) : array();

        erLhcoreClassChat::prefillGetAttributesObject($chat, $attributes, array(), array('do_not_clean' => true));

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => $chat));
        exit;
    }

    erLhcoreClassRestAPIHandler::outputResponse(array
        (
            'error' => false,
            'result' => $chat
        )
    );

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'errors' => (isset($errors) ? $errors : array()),
        'result' => $e->getMessage()
    ));
}

exit();

