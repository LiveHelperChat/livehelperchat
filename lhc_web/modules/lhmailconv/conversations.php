<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/conversations.tpl.php');

if ($currentUser->hasAccessTo('lhmailconv','delete_conversation')) {
    if ( isset($_POST['doDelete']) ) {
        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            erLhcoreClassModule::redirect('mailconv/conversations');
            exit;
        }

        $definition = array(
            'ConversationID' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'ConversationID' ) && !empty($form->ConversationID) ) {
            $chats = erLhcoreClassModelMailconvConversation::getList(array('filterin' => array('id' => $form->ConversationID)));
            foreach ($chats as $chatToDelete) {
                $chatToDelete->removeThis();
            }
        }
    }
}

if ( isset($_POST['doClose']) ) {
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailconv/conversations');
        exit;
    }

    $definition = array(
        'ConversationID' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'ConversationID' ) && !empty($form->ConversationID) ) {
        $chats = erLhcoreClassModelMailconvConversation::getList(array('filterin' => array('id' => $form->ConversationID)));
        $userData = $currentUser->getUserData(true);

        foreach ($chats as $chatToClose) {
            if ($currentUser->hasAccessTo('lhmailconv','close_all_conversation') || erLhcoreClassChat::hasAccessToWrite($chatToClose) )
            {
                erLhcoreClassMailconvWorkflow::closeConversation(['conv' => $chatToClose, 'user_id' => $currentUser->getUserID()]);
                erLhcoreClassMailconvWorkflow::logInteraction($userData->name_support . ' [' . $userData->id.'] '.erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has closed a conversation from a list.'), $userData->name_support, $chatToClose->id);
            }
        }
    }
}

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

/**
 * Departments filter
 * */
$limitation = erLhcoreClassChat::getDepartmentLimitation('lhc_mailconv_conversation', ['check_list_permissions' => true, 'check_list_scope' => 'mails']);

if ($limitation !== false) {
    if ($limitation !== true) {
        $filterParams['filter']['customfilter'][] = $limitation;
    }
} else {
    $filterParams['filter']['customfilter'][] = '1 = -1';
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams,'lhc_mailconv_conversation');

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.list_filter',array('filter' => & $filterParams, 'uparams' => $Params['user_parameters_unordered']));

// Merged id's support
if (isset($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'])) {
    $idsRelated = array_unique(erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id_old' => $filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`']]], '', false, 'conversation_id', false, true, true));
    if (!empty($idsRelated)) {
        $filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'] = array_merge($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'],$idsRelated);
    }
}

if (in_array($Params['user_parameters_unordered']['export'], array(1))) {
    if (ezcInputForm::hasPostData()) {
        session_write_close();
        erLhcoreClassMailconvExport::export(array_merge($filterParams['filter'], array('limit' => 100000, 'offset' => 0)), array('csv' => isset($_POST['CSV']), 'type' => (isset($_POST['exportOptions']) ? $_POST['exportOptions'] : [])));
        exit;
    } else {
        $tpl = erLhcoreClassTemplate::getInstance('lhmailconv/export_config.tpl.php');
        $tpl->set('action_url', erLhcoreClassDesign::baseurl('mailconv/conversations') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
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
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('mailconv/conversations') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
    if (ezcInputForm::hasPostData()) {
        $Errors = erLhcoreClassAdminChatValidatorHelper::validateSavedSearch($savedSearch, array('filter' => $filterParams['filter'], 'input_form' => $filterParams['input_form']));
        if (empty($Errors)) {
            $savedSearch->user_id = $currentUser->getUserID();
            $savedSearch->scope = 'mail';
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



if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 3 && $currentUser->hasAccessTo('lhmailconv','quick_actions')) {
    $tpl = erLhcoreClassTemplate::getInstance('lhviews/quick_actions.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('mailconv/conversations') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
    $tpl->set('update_records',erLhcoreClassModelMailconvConversation::getCount($filterParams['filter']));

    if (ezcInputForm::hasPostData()) {
        if (isset($_POST['new_user_id']) && is_numeric($_POST['new_user_id']) && $_POST['new_user_id'] > 0) {
            $q = ezcDbInstance::get()->createUpdateQuery();
            $conditions = erLhcoreClassModelMailconvConversation::getConditions($filterParams['filter'], $q);
            $q->update( 'lhc_mailconv_conversation' )
                ->set( 'user_id',  (int)$_POST['new_user_id'] )
                ->where(
                    $conditions
                );
            $stmt = $q->prepare();
            $stmt->execute();
            $tpl->set('updated', true);
        } else {
            $tpl->set('errors', ['Please choose an operator']);
        }
    }
    echo $tpl->fetch();
    exit;
}

if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 4) {
    $tpl = erLhcoreClassTemplate::getInstance('lhmailconv/delete_conversations.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('mailconv/conversations') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));

    if (ezcInputForm::hasPostData()) {
        session_write_close();
        $filterParams['filter']['limit'] = 20;
        $filterParams['filter']['offset'] = 0;

        foreach (erLhcoreClassModelMailconvConversation::getList($filterParams['filter']) as $item){
            $item->removeThis();
        }

        erLhcoreClassRestAPIHandler::setHeaders();
        echo json_encode(['left_to_delete' => erLhcoreClassModelMailconvConversation::getCount($filterParams['filter'])]);
        exit;
    }

    $tpl->set('update_records',erLhcoreClassModelMailconvConversation::getCount($filterParams['filter']));

    echo $tpl->fetch();
    exit;
}

if (is_numeric($filterParams['input_form']->has_attachment)) {
    if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) {
        $filterParams['filter']['filterin']['has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    } else if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) {
        $filterParams['filter']['filterin']['has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    } else if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) {
        $filterParams['filter']['filterin']['has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    }
}

if (is_array($filterParams['input_form']->subject_id) && !empty($filterParams['input_form']->subject_id)) {
    erLhcoreClassChat::validateFilterIn($filterParams['input_form']->subject_id);
    $filterParams['filter']['innerjoin']['lhc_mailconv_msg_subject'] = array('`lhc_mailconv_msg_subject`.`conversation_id`','`lhc_mailconv_conversation` . `id`');
    $filterParams['filter']['filterin']['`lhc_mailconv_msg_subject`.`subject_id`'] = $filterParams['input_form']->subject_id;
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

$filterWithoutSort = $filterParams['filter'];
unset($filterWithoutSort['sort']);

$rowsNumber = null;

if (empty($filterWithoutSort)) {
    $rowsNumber = ($rowsNumber = erLhcoreClassModelMailconvConversation::estimateRows()) && $rowsNumber > 10000 ? $rowsNumber : null;
}

try {
    $pages = new lhPaginator();
    $pages->items_total = is_numeric($rowsNumber) ? $rowsNumber : erLhcoreClassModelMailconvConversation::getCount($filterParams['filter']);
    $pages->translationContext = 'chat/activechats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('mailconv/conversations') . $append;
    if ($filterParams['input']->ipp > 0) {
        $pages->setItemsPerPage($filterParams['input']->ipp);
    } else {
        $pages->setItemsPerPage(60);
    }
    $pages->paginate();
    $tpl->set('pages',$pages);

    if ($pages->items_total > 0) {
        $items = erLhcoreClassModelMailconvConversation::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));

        $iconsAdditional = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => false, 'filter' => array('icon_mode' => 1, 'enabled' => 1, 'mail_enabled' => 1)));
        $iconsAdditionalColumn = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => 'position ASC, id ASC','filter' => array('enabled' => 1, 'icon_mode' => 0, 'mail_list_enabled' => 1)));

        erLhcoreClassChat::prefillGetAttributes($items, array(), array(), array('additional_columns' => ($iconsAdditional + $iconsAdditionalColumn), 'do_not_clean' => true));
        
        $tpl->set('icons_additional',$iconsAdditional);
        $tpl->set('additional_chat_columns',$iconsAdditionalColumn);

        $subjectsChats = erLhcoreClassModelMailconvMessageSubject::getList(array('filterin' => array('conversation_id' => array_keys($items))));
        erLhcoreClassChat::prefillObjects($subjectsChats, array(
            array(
                'subject_id',
                'subject',
                'erLhAbstractModelSubject::getList'
            ),
        ));
        foreach ($subjectsChats as $chatSubject) {
            if (!is_array($items[$chatSubject->conversation_id]->subjects)) {
                $items[$chatSubject->conversation_id]->subjects = [];
            }
            $items[$chatSubject->conversation_id]->subjects[] = $chatSubject->subject;
        }

        $tpl->set('items',$items);
    }

} catch (Exception $e) {
    $tpl->set('takes_to_long',true);
    $pages = new lhPaginator();
    $pages->items_total = 0;
    $pages->translationContext = 'chat/pendingchats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('mailconv/conversations') . $append;
    $pages->paginate();
    $tpl->set('pages',$pages);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailconv/conversations');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('can_delete',$currentUser->hasAccessTo('lhmailconv','delete_conversation'));
$tpl->set('can_close',$currentUser->hasAccessTo('lhmailconv','close_all_conversation'));

$Result['content'] = $tpl->fetch();

?>