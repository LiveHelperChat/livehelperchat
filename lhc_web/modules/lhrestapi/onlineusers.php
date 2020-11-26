<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();
    $params = array();

    if (isset($_GET['dep_id']) && is_numeric($_GET['dep_id'])) {
        $params['filter']['dep_id'] = (int)$_GET['dep_id'];
    }

    $filter = array();
    $filter['customfilter'][] = '(last_activity > ' . (int)(time() - (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout']) . ' OR always_on = 1)';

    $filter['limit'] = false;
    $filter['sort'] = 'active_chats DESC, hide_online ASC';
    $filter['group'] = 'user_id';
    $filter = array_merge_recursive($filter, $params);
    $filter['ignore_fields'] = array('id','dep_id','hide_online_ts','hide_online','last_activity','lastd_activity','always_on','last_accepted','active_chats','pending_chats','inactive_chats');
    $filter['select_columns'] = '
        max(`id`) as `id`, 
        max(`dep_id`) as `dep_id`,
        max(`hide_online_ts`) as `hide_online_ts`,
        max(`hide_online`) as `hide_online`,
        max(`last_activity`) as `last_activity`, 
        max(`lastd_activity`) as `lastd_activity`, 
        max(`always_on`) as `always_on`, 
        max(`last_accepted`) as `last_accepted`,
        max(`active_chats`) as `active_chats`,
        max(`pending_chats`) as `pending_chats`,
        max(`inactive_chats`) as `inactive_chats`';

    $onlineOperators = erLhcoreClassModelUserDep::getList($filter);

    if (isset($_GET['include_user']) && $_GET['include_user'] == 'true') {
        $userIDs = array();
        foreach ($onlineOperators as $onlineOperator) {
            $userIDs[] = $onlineOperator->user_id;
        }

        if (!empty($userIDs)) {
            $operators = erLhcoreClassModelUser::getList(array('filterin' => array('id' => $userIDs)));
            foreach($operators as $index => $user)
            {
                // loose password
                unset($user->password);
                $operators[$index] = $user;
            }
        }

        foreach ($onlineOperators as $index => $onlineOperator) {
            $onlineOperators[$index]->user = isset($operators[$onlineOperator->user_id]) ? $operators[$onlineOperator->user_id] : new stdClass();
        }
    }

    erLhcoreClassRestAPIHandler::outputResponse(array_values($onlineOperators));

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();




