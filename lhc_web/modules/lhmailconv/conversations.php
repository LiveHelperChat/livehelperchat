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
            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => $chatToClose, 'user_id' => $currentUser->getUserID()]);
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
$limitation = erLhcoreClassChat::getDepartmentLimitation('lhc_mailconv_conversation');

if ($limitation !== false) {

    if ($limitation !== true) {
        $filterParams['filter']['customfilter'][] = $limitation;
    }

    $filterParams['filter']['smart_select'] = true;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.list_filter',array('filter' => & $filterParams, 'uparams' => $Params['user_parameters_unordered']));

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

if ($filterParams['input_form']->subject_id > 0) {
    $filterParams['filter']['innerjoin']['lhc_mailconv_msg_subject'] = array('`lhc_mailconv_msg_subject`.`conversation_id`','`lhc_mailconv_conversation` . `id`');
    $filterParams['filter']['filter']['`lhc_mailconv_msg_subject`.`subject_id`'] = $filterParams['input_form']->subject_id;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvConversation::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailconv/conversations') . $append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvConversation::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailconv/conversations');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('can_delete',$currentUser->hasAccessTo('lhmailconv','delete_conversation'));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Conversations'))
);

?>