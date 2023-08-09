<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/lists.tpl.php');

if ( isset($_POST['doDelete']) ) {
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('chat/list');
		exit;
	}

	$definition = array(
		'ChatID' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
		),
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'ChatID' ) && !empty($form->ChatID) ) {
		$chats = erLhcoreClassChat::getList(array('filterin' => array('id' => $form->ChatID)));
		foreach ($chats as $chatToDelete) {
			if (erLhcoreClassChat::hasAccessToWrite($chatToDelete) && ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chatToDelete->user_id == $currentUser->getUserID())))
			{
				$chatToDelete->removeThis();
			}
		}
	}
}

if ( isset($_POST['doClose']) ) {
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('chat/list');
		exit;
	}

	$definition = array(
		'ChatID' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
		),
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'ChatID' ) && !empty($form->ChatID) ) {
		$chats = erLhcoreClassChat::getList(array('filterin' => array('id' => $form->ChatID)));
        $userData = $currentUser->getUserData(true);

		foreach ($chats as $chatToClose) {
            if (($chatToClose->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote')) && erLhcoreClassChat::hasAccessToWrite($chatToClose))
            {
                erLhcoreClassChatHelper::closeChat(array(
                    'user' => $userData,
                    'chat' => $chatToClose,
                ));
            }
		}
	}
}

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams);

if (is_array($filterParams['input_form']->subject_id) && !empty($filterParams['input_form']->subject_id)) {
    erLhcoreClassChat::validateFilterIn($filterParams['input_form']->subject_id);
    $filterParams['filter']['innerjoin']['lh_abstract_subject_chat'] = array('`lh_abstract_subject_chat`.`chat_id`','`lh_chat` . `id`');
    $filterParams['filter']['filterin']['`lh_abstract_subject_chat`.`subject_id`'] = $filterParams['input_form']->subject_id;
}

/**
 * Departments filter
 * */
$limitation = erLhcoreClassChat::getDepartmentLimitation( 'lh_chat', ['check_list_permissions' => true]);

if ($limitation !== false) {
    if ($limitation !== true) {
        $filterParams['filter']['customfilter'][] = $limitation;
    }
} else {
    $filterParams['filter']['customfilter'][] = '1 = -1';
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.list_filter',array('filter' => & $filterParams, 'uparams' => $Params['user_parameters_unordered']));

if ($Params['user_parameters_unordered']['print'] == 1) {
	$tpl = erLhcoreClassTemplate::getInstance('lhchat/printchats.tpl.php');
	$items = erLhcoreClassChat::getList(array_merge($filterParams['filter'],array('limit' => 100000,'offset' => 0)));
	$tpl->set('items',$items);
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'popup';
	return;
}

if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 1) {
    if (ezcInputForm::hasPostData()) {
        session_write_close();
        $ignoreFields = (new erLhcoreClassModelChat)->getState();
        unset($ignoreFields['id']);
        $ignoreFields = array_keys($ignoreFields);
        erLhcoreClassChatExport::chatListExportXLS(erLhcoreClassModelChat::getList(array_merge($filterParams['filter'], array('limit' => 100000, 'offset' => 0, 'ignore_fields' => $ignoreFields))), array('csv' => isset($_POST['CSV']), 'type' => (isset($_POST['exportOptions']) ? $_POST['exportOptions'] : [])));
        exit;
    } else {
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/export_config.tpl.php');
        $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/list') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
        echo $tpl->fetch();
        exit;
    }
}

if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 2) {

    $savedSearch = new erLhAbstractModelSavedSearch();

    if ($Params['user_parameters_unordered']['view'] > 0) {
        $savedSearchPresent = erLhAbstractModelSavedSearch::fetch($Params['user_parameters_unordered']['view']);
        if ($savedSearchPresent->user_id == $currentUser->getUserID()) {
            $savedSearch = $savedSearchPresent;
        }
    }

    $tpl = erLhcoreClassTemplate::getInstance('lhviews/save_chat_view.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/list') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
    if (ezcInputForm::hasPostData()) {

        $Errors = erLhcoreClassAdminChatValidatorHelper::validateSavedSearch($savedSearch, array('filter' => $filterParams['filter'], 'input_form' => $filterParams['input_form']));

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $Errors[] = 'Invalid CSRF token!';
        }

        if (empty($Errors)) {
            $savedSearch->user_id = $currentUser->getUserID();
            $savedSearch->scope = 'chat';
            $savedSearch->saveThis();
            $tpl->set('updated', true);
        } else {
            $tpl->set('errors', $Errors);
        }
    }
    $tpl->set('item', $savedSearch);
    echo $tpl->fetch();
    exit;
}

if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 3) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/delete_chats.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/list') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));

    if (ezcInputForm::hasPostData()) {
        session_write_close();
        $filterParams['filter']['limit'] = 20;
        $filterParams['filter']['offset'] = 0;

        foreach (erLhcoreClassModelChat::getList($filterParams['filter']) as $item){
            $item->removeThis();
        }

        erLhcoreClassRestAPIHandler::setHeaders();
        echo json_encode(['left_to_delete' => erLhcoreClassModelChat::getCount($filterParams['filter'])]);
        exit;
    }

    $tpl->set('update_records',erLhcoreClassModelChat::getCount($filterParams['filter']));

    echo $tpl->fetch();
    exit;
}

$db = ezcDbInstance::get();

try {
    $db->query("SET SESSION wait_timeout=2");
} catch (Exception $e){
    //
}

try {
    $db->query("SET SESSION interactive_timeout=5");} catch (Exception $e){
} catch (Exception $e) {
    //
}

try {
    $db->query("SET SESSION innodb_lock_wait_timeout=5");
} catch (Exception $e) {
    //
}

try {
    $db->query("SET SESSION max_execution_time=5000;");
} catch (Exception $e) {
    //
}

try {
    $db->query("SET SESSION max_statement_time=5;");
} catch (Exception $e) {
    // Ignore we try to limit how long query can run
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$rowsNumber = null;

if (empty($filterParams['filter'])) {
    $rowsNumber = ($rowsNumber = erLhcoreClassModelChat::estimateRows()) && $rowsNumber > 10000 ? $rowsNumber : null;
}

try {
    $pages = new lhPaginator();
    $pages->items_total = is_numeric($rowsNumber) ? $rowsNumber : erLhcoreClassModelChat::getCount($filterParams['filter']);
    $pages->translationContext = 'chat/pendingchats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('chat/list').$append;
    $pages->paginate();
    $tpl->set('pages',$pages);

    if ($pages->items_total > 0) {
        $items = erLhcoreClassModelChat::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
        $iconsAdditional = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => false, 'filter' => array('icon_mode' => 1, 'enabled' => 1, 'chat_enabled' => 1)));
        $iconsAdditionalColumn = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => 'position ASC, id ASC','filter' => array('enabled' => 1, 'icon_mode' => 0, 'chat_list_enabled' => 1)));

        erLhcoreClassChat::prefillGetAttributes($items, array(), array(), array('additional_columns' => ($iconsAdditional + $iconsAdditionalColumn), 'do_not_clean' => true));
        $tpl->set('icons_additional',$iconsAdditional);
        $tpl->set('additional_chat_columns',$iconsAdditionalColumn);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.list_items',array('filter' => & $items, 'uparams' => $Params['user_parameters_unordered']));

        $subjectsChats = erLhAbstractModelSubjectChat::getList(array('filterin' => array('chat_id' => array_keys($items))));
        erLhcoreClassChat::prefillObjects($subjectsChats, array(
            array(
                'subject_id',
                'subject',
                'erLhAbstractModelSubject::getList'
            ),
        ));
        foreach ($subjectsChats as $chatSubject) {
            if (!is_array($items[$chatSubject->chat_id]->subjects)) {
                $items[$chatSubject->chat_id]->subjects = [];
            }
            $items[$chatSubject->chat_id]->subjects[] = $chatSubject->subject;
        }

        $tpl->set('items',$items);
    }

} catch (Exception $e) {
    $tpl->set('takes_to_long',true);
    $pages = new lhPaginator();
    $pages->items_total = 0;
    $pages->translationContext = 'chat/pendingchats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('chat/list').$append;
    $pages->paginate();
    $tpl->set('pages',$pages);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/list');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('can_delete_global',$currentUser->hasAccessTo('lhchat','deleteglobalchat'));
$tpl->set('can_delete_general',$currentUser->hasAccessTo('lhchat','deletechat'));
$tpl->set('can_close_global',$currentUser->hasAccessTo('lhchat','allowcloseremote'));
$tpl->set('current_user_id',$currentUser->getUserID());

$Result['content'] = $tpl->fetch();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.list_path',array('result' => & $Result));
?>
