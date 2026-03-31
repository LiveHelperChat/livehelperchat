<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/userlist.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'user','module_file' => 'user_list','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

erLhcoreClassChatStatistic::formatUserFilter($filterParams, 'lh_users', 'id', ['group_id','group_ids']);

$customFiltersDep = [];

if (isset($filterParams['input']->department_group_ids) &&  is_array($filterParams['input']->department_group_ids) && !empty($filterParams['input']->department_group_ids)) {
    erLhcoreClassChat::validateFilterIn($filterParams['input']->department_group_ids);
    $customFiltersDep[] = '`lh_users`.`id` IN (SELECT `user_id` FROM `lh_userdep` WHERE `dep_id` IN (SELECT `dep_id` FROM `lh_departament_group_member` WHERE `dep_group_id` IN (' . implode(',',$filterParams['input']->department_group_ids) . ')))';
}

if (isset($filterParams['input']->department_ids) &&  is_array($filterParams['input']->department_ids) && !empty($filterParams['input']->department_ids)) {
    erLhcoreClassChat::validateFilterIn($filterParams['input']->department_ids);
    $customFiltersDep[] = '`lh_users`.`id` IN (SELECT user_id FROM lh_userdep WHERE dep_id IN (' . implode(',',$filterParams['input']->department_ids) . '))';
}

if (!empty($customFiltersDep)) {
    $filterParams['filter']['customfilter'][] = '('.implode(' OR ',$customFiltersDep).')';
}

/*if (isset($filterParams['filter']['filtergte']['`p1`.`ctime`']) || isset($filterParams['filter']['filterlte']['`p1`.`ctime`'])) {
    $filterParams['filter']['innerjoin'] = array('lh_users_login as p1' => array('`p1`.`user_id`', '`lh_users`.`id`'));
    $filterParams['filter']['leftouterjoin'] = array('lh_users_login as p2' => '(`p2`.`user_id` = `lh_users`.`id` AND p1.id < p2.id)');
    $filterParams['filter']['customfilter'][] = 'p2.id IS NULL';
}*/

if (is_array($filterParams['input_form']->user_languages) && !empty($filterParams['input_form']->user_languages)) {
    $filterParams['filter']['innerjoin']['lh_speech_user_language'] = array('`lh_speech_user_language`.`user_id`','`lh_users` . `id`');
    $filterParams['filter']['filterin']['`lh_speech_user_language`.`language`'] = $filterParams['input_form']->user_languages;
}

if ($Params['user_parameters_unordered']['export'] == 1) {
    erLhcoreClassChatExport::exportUsers(erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'],array('limit' => false,'sort' => 'id DESC'))));
}

if ($Params['user_parameters_unordered']['export'] == 'quick_actions' && erLhcoreClassUser::instance()->hasAccessTo('lhuser','quick_actions')) {
    $tpl = erLhcoreClassTemplate::getInstance('lhuser/parts/quick_actions.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('user/userlist') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
    $tpl->set('update_records',erLhcoreClassModelUser::getUserCount($filterParams['filter']));

    if (ezcInputForm::hasPostData()) {

        $directUserActions = [
            'disable_operators' => ['column' => 'disabled',      'value' => 1],
            'force_logout'      => ['column' => 'force_logout',   'value' => 1],
            'auto_accept_on'    => ['column' => 'auto_accept',    'value' => 1],
            'exclude_auto_assign'   => ['column' => 'exclude_autoasign',    'value' => 1],
            'include_auto_assign'   => ['column' => 'exclude_autoasign',    'value' => 0],
        ];

        foreach ($directUserActions as $postKey => $action) {
            if (isset($_POST[$postKey]) && $_POST[$postKey] == 'on') {
                $q = ezcDbInstance::get()->createUpdateQuery();
                $conditions = erLhcoreClassModelUser::getConditions($filterParams['filter'], $q);
                $q->update('lh_users')->set($action['column'], $action['value']);
                if (!empty($conditions)) {
                    $q->where($conditions);
                }
                $q->prepare()->execute();

                // Update auto assignment values
                if (in_array($postKey, ['exclude_auto_assign','include_auto_assign'])) {
                    foreach (erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'], array('limit' => false))) as $userItem) {
                        $db = ezcDbInstance::get();
                        $stmt = $db->prepare('UPDATE lh_userdep SET exclude_autoasign = :exclude_autoasign WHERE user_id = :user_id');
                        $stmt->bindValue(':user_id', $userItem->id, PDO::PARAM_INT);
                        $stmt->bindValue(':exclude_autoasign', $action['value'], PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }
        }

        $settingActions = [
            'auto_preload' => ['identifier' => 'auto_preload', 'value' => 1],
            'chat_tabs_on' => ['identifier' => 'hide_tabs',    'value' => 0],
            'chat_tabs_off' => ['identifier' => 'hide_tabs',   'value' => 1],
            'show_alert_transfer_off' => ['identifier' => 'show_alert_transfer', 'value' => 0],
            'show_alert_transfer_on' => ['identifier' => 'show_alert_transfer', 'value' => 1],
            'notification_only_assigned_on' => ['identifier' => 'ownntfonly', 'value' => 1],
            'notification_only_assigned_off' => ['identifier' => 'ownntfonly', 'value' => 0]
        ];

        foreach ($settingActions as $postKey => $action) {
            if (isset($_POST[$postKey]) && $_POST[$postKey] == 'on') {
                $userIds = array();
                foreach (erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'], array('limit' => false))) as $userItem) {
                    $userIds[] = $userItem->id;
                }

                if (!empty($userIds)) {
                    $db = ezcDbInstance::get();
                    $q = $db->createDeleteQuery();
                    $q->deleteFrom('lh_users_setting')->where(
                        $q->expr->lAnd(
                            $q->expr->in('user_id', $userIds),
                            $q->expr->eq('identifier', $q->bindValue($action['identifier']))
                        )
                    );
                    $q->prepare()->execute();

                    foreach ($userIds as $userId) {
                        erLhcoreClassModelUserSetting::setSetting($action['identifier'], $action['value'], $userId);
                    }
                }
            }
        }

        if (isset($_POST['change_password']) && $_POST['change_password'] == 'on') {
            foreach (erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'],array('offset' => 0, 'limit' => false))) as $item) {
                if (erLhcoreClassModelUserLogin::getCount(array('filter' => array (
                    'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                    'status' => erLhcoreClassModelUserLogin::STATUS_PENDING,
                    'user_id' => $item->id))) == 0) {
                        erLhcoreClassModelUserLogin::logUserAction(array(
                            'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                            'user_id' => $item->id,
                            'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password reset requested by') . ' ' . $currentUser->getUserData(),
                        ));
                }
            }
        }

        $tpl->set('updated', true);
    }

    echo $tpl->fetch();
    exit;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/userlist') . $append;
$pages->items_total = erLhcoreClassModelUser::getUserCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$userlist = erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC')));

$tpl->set('userlist',$userlist);
$tpl->set('pages',$pages);
$tpl->set('currentUser',$currentUser);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('user/userlist');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.userlist_path',array('result' => & $Result));

?>