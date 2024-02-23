<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/listarchivemails.tpl.php');

$archive = \LiveHelperChat\Models\mailConv\Archive\Range::fetch($Params['user_parameters']['id']);

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

// Chat id has to be replaced to table one
if (isset($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'])) {
    $filterParams['filter']['filter'][ '`'. \LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '`.`id`'] = $filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'];
    unset($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`']);
}

// Set correct archive tables
$archive->setTables();

$filterParams['filter']['sort'] = '`' . \LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '`.`id` DESC';

if (is_numeric($filterParams['input_form']->has_attachment)) {
    if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) {
        $filterParams['filter']['filterin'][\LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '.has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    } else if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) {
        $filterParams['filter']['filterin'][\LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '.has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    } else if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) {
        $filterParams['filter']['filterin'][\LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '.has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    } else if ($filterParams['input_form']->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) {
        $filterParams['filter']['filter'][\LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '.has_attachment'] = erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY;
    } else if ($filterParams['input_form']->has_attachment == 5) { // No attachment (inline)
        $filterParams['filter']['filternotin'][\LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '.has_attachment'] = [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    } else if ($filterParams['input_form']->has_attachment == 4) { // No attachment (as file)
        $filterParams['filter']['filternotin'][\LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '.has_attachment'] =  [
            erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
            erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
        ];
    }
}

if (is_numeric($filterParams['input_form']->is_external)) {
    $filterParams['filter']['innerjoin'][\LiveHelperChat\Models\mailConv\Archive\Message::$dbTable] = array('`'.\LiveHelperChat\Models\mailConv\Archive\Message::$dbTable.'`.`conversation_id`','`'. \LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '` . `id`');
    $filterParams['filter']['filterin']['`'.\LiveHelperChat\Models\mailConv\Archive\Message::$dbTable.'`.`is_external`'] = $filterParams['input_form']->is_external;
}

if (is_array($filterParams['input_form']->subject_id) && !empty($filterParams['input_form']->subject_id)) {
    erLhcoreClassChat::validateFilterIn($filterParams['input_form']->subject_id);
    $filterParams['filter']['innerjoin'][ \LiveHelperChat\Models\mailConv\Archive\MessageSubject::$dbTable ] = array('`'. \LiveHelperChat\Models\mailConv\Archive\MessageSubject::$dbTable . '`.`conversation_id`','`'. \LiveHelperChat\Models\mailConv\Archive\Conversation::$dbTable . '` . `id`');
    $filterParams['filter']['filterin']['`'. \LiveHelperChat\Models\mailConv\Archive\MessageSubject::$dbTable . '`.`subject_id`'] = $filterParams['input_form']->subject_id;
}

if (in_array($Params['user_parameters_unordered']['export'], array(1))) {
    if (ezcInputForm::hasPostData()) {
        session_write_close();
        erLhcoreClassMailconvExport::export(array_merge($filterParams['filter'], array('limit' => 100000, 'offset' => 0)), array('is_archive' => true, 'csv' => isset($_POST['CSV']), 'type' => (isset($_POST['exportOptions']) ? $_POST['exportOptions'] : [])));
        exit;
    } else {
        $tpl = erLhcoreClassTemplate::getInstance('lhmailconv/export_config.tpl.php');
        $tpl->set('action_url', erLhcoreClassDesign::baseurl('mailarchive/listarchivemails') . '/' . $archive->id . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
        echo $tpl->fetch();
        exit;
    }
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('mailarchive/listarchivemails').'/'.$archive->id.$append;
$pages->items_total = \LiveHelperChat\Models\mailConv\Archive\Conversation::getCount($filterParams['filter']);
if ($filterParams['input']->ipp > 0) {
    $pages->setItemsPerPage($filterParams['input']->ipp);
} else {
    $pages->setItemsPerPage(60);
}
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    try {
        $items = \LiveHelperChat\Models\mailConv\Archive\Conversation::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),$filterParams['filter']));

        $iconsAdditional = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => false, 'filter' => array('icon_mode' => 1, 'enabled' => 1, 'mail_enabled' => 1)));
        $iconsAdditionalColumn = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => 'position ASC, id ASC','filter' => array('enabled' => 1, 'icon_mode' => 0, 'mail_list_enabled' => 1)));

        erLhcoreClassChat::prefillGetAttributes($items, array(), array(), array('additional_columns' => ($iconsAdditional + $iconsAdditionalColumn), 'do_not_clean' => true));

        $tpl->set('icons_additional',$iconsAdditional);
        $tpl->set('additional_chat_columns',$iconsAdditionalColumn);

        $subjectsChats = \LiveHelperChat\Models\mailConv\Archive\MessageSubject::getList(array('filterin' => array('conversation_id' => array_keys($items))));
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

    } catch (Exception $e) {
        print_r($e->getMessage());
    }
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailarchive/listarchivemails').'/'.$archive->id;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('items',$items);
$tpl->set('archive',$archive);
$tpl->set('pages',$pages);
$tpl->set('can_delete',erLhcoreClassUser::instance()->hasAccessTo('lhmailarchive','configuration'));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('mailarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')),
    array('url' => erLhcoreClassDesign::baseurl('mailarchive/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archived mails'));




?>