<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'userlistonlineall')) {
        throw new Exception('You do not have permission. `lhuser`, `userlistonlineall` is required.');
    }

    // ezcDbInstance::get()->query("set session sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION,ONLY_FULL_GROUP_BY'");

    $params = array();

    if (isset($_GET['dep_id']) && is_numeric($_GET['dep_id'])) {
        $params['filter']['dep_id'] = (int)$_GET['dep_id'];
    }

    $filter = array();

    $timeoutValue = (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['online_timeout'];

    if (!isset($_GET['include_offline']) || $_GET['include_offline'] !== 'true') {
        $filter['customfilter'][] = '((last_activity > ' . (int)(time() - $timeoutValue) . ' OR `lh_userdep`.`always_on` = 1) AND `lh_userdep`.`hide_online` = 0 AND ro = 0)';
    }

    $filter['innerjoin'] = array('`lh_users`' => array('`lh_userdep`.`user_id`', '`lh_users`.`id`'));
    $filter['limit'] = false;
    $filter['sort'] = 'active_chats DESC, `lh_userdep`.`hide_online` ASC';
    $filter['group'] = 'user_id';
    $filter = array_merge_recursive($filter, $params);
    $filter['ignore_fields'] = array('chat_max_priority','chat_min_priority','assign_priority', 'max_mails','last_accepted_mail','exc_indv_autoasign','exclude_autoasign_mails','active_mails','pending_mails','exclude_autoasign','max_chats','dep_group_id','type','ro','id','dep_id','hide_online_ts','hide_online','last_activity','lastd_activity','always_on','last_accepted','active_chats','pending_chats','inactive_chats','ro');

    $filter['select_columns'] = '
         max(`lh_userdep`.`id`) as `id`, 
        max(`ro`) as `ro`,
        max(`max_chats`) as `max_chats`,
        max(`max_mails`) as `max_mails`,
        max(`dep_id`) as `dep_id`,
        max(`hide_online_ts`) as `hide_online_ts`,
        max(`lh_userdep`.`hide_online`) as `hide_online`,
        max(`last_activity`) as `last_activity`, 
        max(`lastd_activity`) as `lastd_activity`, 
        max(`lh_userdep`.`always_on`) as `always_on`, 
        max(`last_accepted`) as `last_accepted`,
        max(`last_accepted_mail`) as `last_accepted_mail`,
        max(`active_chats`) as `active_chats`,
        max(`pending_chats`) as `pending_chats`,
        max(`inactive_chats`) as `inactive_chats`,
        max(`active_mails`) as `active_mails`,
        max(`pending_mails`) as `pending_mails`,
        min(`ro`) as `ro`';

    $onlineOperators = erLhcoreClassModelUserDep::getList($filter);

    foreach ($onlineOperators as $onlineOperatorIndex => $onlineOperator) {
        $onlineOperators[$onlineOperatorIndex]->is_online = ($onlineOperator->last_activity > (time() - $timeoutValue) || $onlineOperator->always_on == 1) && $onlineOperator->hide_online == 0 && $onlineOperator->ro == 0;
    }

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
        if (isset($operators[$onlineOperator->user_id])) {
            $onlineOperators[$index]->invisible_mode = $operators[$onlineOperator->user_id]->invisible_mode;
        }

        if (isset($_GET['include_user']) && $_GET['include_user'] == 'true') {
            $onlineOperators[$index]->user = isset($operators[$onlineOperator->user_id]) ? $operators[$onlineOperator->user_id] : new stdClass();
        }

        if (
            !isset($operators[$onlineOperator->user_id]) ||
            ($onlineOperators[$index]->invisible_mode == 1 && isset($_GET['exclude_invisible']) && $_GET['exclude_invisible'] === 'true') ||
            (((isset($_GET['include_disabled']) && $_GET['include_disabled'] === 'false') || !isset($_GET['include_disabled'])) && $operators[$onlineOperator->user_id]->disabled == 1)
        ) {
            unset($onlineOperators[$index]);
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




