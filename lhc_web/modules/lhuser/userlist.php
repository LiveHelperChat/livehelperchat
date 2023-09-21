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

erLhcoreClassChatStatistic::formatUserFilter($filterParams, 'lh_users', 'id');

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

        if (isset($_POST['disable_operators']) && $_POST['disable_operators'] == 'on') {
            $q = ezcDbInstance::get()->createUpdateQuery();
            $conditions = erLhcoreClassModelUser::getConditions($filterParams['filter'], $q);
            $q->update( 'lh_users' )->set( 'disabled',  1);
            if (!empty($conditions)) {
                $q->where(
                    $conditions
                );
            }
            $stmt = $q->prepare();
            $stmt->execute();
        }

        if (isset($_POST['force_logout']) && $_POST['force_logout'] == 'on') {
            $q = ezcDbInstance::get()->createUpdateQuery();
            $conditions = erLhcoreClassModelUser::getConditions($filterParams['filter'], $q);
            $q->update( 'lh_users' )->set( 'force_logout',  1);
            if (!empty($conditions)) {
                $q->where(
                    $conditions
                );
            }
            $stmt = $q->prepare();
            $stmt->execute();
        }

        if (isset($_POST['auto_preload']) && $_POST['auto_preload'] == 'on') {
            $q = ezcDbInstance::get()->createDeleteQuery();
            $conditions = erLhcoreClassModelUser::getConditions($filterParams['filter'], $q);
            $conditions[] = $q->expr->eq('identifier', $q->bindValue('auto_preload'));
            $q->deleteFrom( 'lh_users_setting' )
                ->where(
                    $conditions
                );
            $stmt = $q->prepare();
            $stmt->execute();
            foreach (erLhcoreClassModelUser::getUserList(array_merge($filterParams['filter'],array( 'limit' => false))) as $userItem) {
                erLhcoreClassModelUserSetting::setSetting('auto_preload',1, $userItem->id);
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