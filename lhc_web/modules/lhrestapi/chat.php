<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'use')) {
        throw new Exception('You do not have permission. `lhchat`, `use` is required.');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $restAPI = array('ignore_captcha' => true, 'ignore_geo' => true, 'collect_all' => true);

        include "modules/lhwidgetrestapi/submitonline.php";

        if ($outputResponse['success'] === false) {
            $errors = $Errors;
            throw new Exception(implode("\n",$outputResponse['errors']));
        }

        if (isset($requestBody['main_attr']) && is_array($requestBody['main_attr'])) {
            $userData = erLhcoreClassRestAPIHandler::getUser();
            $updated = false;
            foreach ($requestBody['main_attr'] as $attr => $valueAttr) {
                if ($attr == 'status') {
                    erLhcoreClassChatHelper::changeStatus(array(
                        'user' => $userData,
                        'chat' => $chat,
                        'status' => $valueAttr,
                        'allow_close_remote' => erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'allowcloseremote')
                    ));
                } elseif ($attr != 'id') { // we never update ID
                    $chat->{$attr} = $valueAttr;
                    $updated = true;
                }
            }
            
            $chat->updateThis();
        }


    } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        if (!erLhcoreClassRestAPIHandler::hasAccessToWrite($chat) || !erLhcoreClassRestAPIHandler::hasAccessToRead($chat)) {
            throw new Exception('You do not have permission. Please check that current user has permission to write to selected department.');
        }

        // Attributes an operator may update through the API. Everything else (id, hash, ip,
        // time, counters, last_msg_id, ...) is ignored, so a full chat object can be posted back.
        $editableAttributes = array(
            'nick', 'email', 'phone', 'city', 'country_code', 'country_name', 'lat', 'lon',
            'remarks', 'additional_data', 'chat_variables', 'product_id', 'unanswered_chat',
            'status_sub', 'status_sub_arg', 'status_sub_sub'
        );

        $restApiUser = erLhcoreClassRestAPIHandler::getUser();

        foreach ($requestBody as $attr => $value) {
            if ($attr == 'id') {
                continue;
            }

            if ($attr == 'status') {
                erLhcoreClassChatHelper::changeStatus(array(
                    'user' => $restApiUser,
                    'chat' => $chat,
                    'status' => (int)$value,
                    'allow_close_remote' => erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'allowcloseremote')
                ));
                continue;
            }

            if ($attr == 'dep_id') {
                $newDepartmentId = (int)$value;

                if ($newDepartmentId != (int)$chat->dep_id) {
                    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'modifychatcore')) {
                        throw new Exception('You do not have permission to change the chat department. `lhchat`, `modifychatcore` is required.');
                    }

                    $readOnlyDepartments = erLhcoreClassUserDep::getUserReadDepartments($restApiUser->id, $restApiUser->cache_version);

                    if (in_array($newDepartmentId, $readOnlyDepartments) ||
                        ($restApiUser->all_departments == 0 && !in_array($newDepartmentId, erLhcoreClassUserDep::getUserDepartaments($restApiUser->id, $restApiUser->cache_version)))) {
                        throw new Exception('You do not have permission to change the chat to the selected department.');
                    }

                    $chat->dep_id = $newDepartmentId;
                }

                continue;
            }

            if ($attr == 'user_id') {
                $newUserId = (int)$value;

                if ($newUserId != (int)$chat->user_id && $newUserId != 0 && $newUserId != erLhcoreClassRestAPIHandler::getUserId() && !erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'modifychatcore')) {
                    throw new Exception('You do not have permission to assign the chat to another operator. `lhchat`, `modifychatcore` is required.');
                }

                $chat->user_id = $newUserId;
                continue;
            }

            if (in_array($attr, $editableAttributes, true)) {
                $chat->{$attr} = $value;
            }
        }

        $chat->saveThis();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        if (
            erLhcoreClassRestAPIHandler::hasAccessToWrite($chat) && erLhcoreClassRestAPIHandler::hasAccessToRead($chat) &&
            (erLhcoreClassRestAPIHandler::hasAccessTo('lhchat','deleteglobalchat') || (erLhcoreClassRestAPIHandler::hasAccessTo('lhchat','deletechat') && $chat->user_id == erLhcoreClassRestAPIHandler::getUserId()))
        ) {
            $chat->removeThis();
        } else {
            throw new Exception('You do not have permission to delete this chat!');
        }

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

        if (!erLhcoreClassRestAPIHandler::hasAccessToRead($chat)) {
            throw new Exception('You do not have permission. Please check that current user has permission to read to selected department.');
        }

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

