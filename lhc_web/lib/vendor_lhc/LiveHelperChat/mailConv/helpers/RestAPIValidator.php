<?php

namespace LiveHelperChat\mailConv\helpers;

class RestAPIValidator
{
    public static function validateConversationList()
    {

        $filterParams = \erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'use_override' => true, 'uparams' => []));

        $limitation = \erLhcoreClassChat::getDepartmentLimitation('lhc_mailconv_conversation', ['rest_api' => true, 'user' => \erLhcoreClassRestAPIHandler::getUser(), 'check_list_permissions' => true, 'check_list_scope' => 'mails']);

        if ($limitation !== false) {
            if ($limitation !== true) {
                $filterParams['filter']['customfilter'][] = $limitation;
            }
        } else {
            $filterParams['filter']['customfilter'][] = '1 = -1';
        }

        \erLhcoreClassChatStatistic::formatUserFilter($filterParams, 'lhc_mailconv_conversation');

        \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.list_filter',array('filter' => & $filterParams, 'uparams' => []));

        // Merged id's support
        if (isset($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'])) {
            $idsRelated = array_unique(\erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id_old' => $filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`']]], '', false, 'conversation_id', false, true, true));
            if (!empty($idsRelated)) {
                $filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'] = array_merge($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'],$idsRelated);
            }
        }

        if (is_numeric($filterParams['input_form']->has_attachment)) {
            if ($filterParams['input_form']->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) {
                $filterParams['filter']['filterin']['lhc_mailconv_conversation.has_attachment'] = [
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
                ];
            } else if ($filterParams['input_form']->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) {
                $filterParams['filter']['filterin']['lhc_mailconv_conversation.has_attachment'] = [
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
                ];
            } else if ($filterParams['input_form']->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) {
                $filterParams['filter']['filterin']['lhc_mailconv_conversation.has_attachment'] = [
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
                ];
            } else if ($filterParams['input_form']->has_attachment == \erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) {
                $filterParams['filter']['filter']['lhc_mailconv_conversation.has_attachment'] = \erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY;
            } else if ($filterParams['input_form']->has_attachment == 5) { // No attachment (inline)
                $filterParams['filter']['filternotin']['lhc_mailconv_conversation.has_attachment'] = [
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE,
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
                ];
            } else if ($filterParams['input_form']->has_attachment == 4) { // No attachment (as file)
                $filterParams['filter']['filternotin']['lhc_mailconv_conversation.has_attachment'] =  [
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE,
                    \erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX
                ];
            }
        }

        if (is_array($filterParams['input_form']->subject_id) && !empty($filterParams['input_form']->subject_id)) {
            \erLhcoreClassChat::validateFilterIn($filterParams['input_form']->subject_id);
            $filterParams['filter']['innerjoin']['lhc_mailconv_msg_subject'] = array('`lhc_mailconv_msg_subject`.`conversation_id`','`lhc_mailconv_conversation` . `id`');
            $filterParams['filter']['filterin']['`lhc_mailconv_msg_subject`.`subject_id`'] = $filterParams['input_form']->subject_id;
        }

        if (is_numeric($filterParams['input_form']->is_external)) {
            $filterParams['filter']['innerjoin']['lhc_mailconv_msg'] = array('`lhc_mailconv_msg`.`conversation_id`','`lhc_mailconv_conversation` . `id`');
            $filterParams['filter']['filterin']['`lhc_mailconv_msg`.`is_external`'] = $filterParams['input_form']->is_external;
        }

        $filterParams['filter']['limit'] = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $filterParams['filter']['offset'] = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $filterParams['filter']['smart_select'] = true;

        $mails = \erLhcoreClassModelMailconvConversation::getList(array_merge(array('limit' => 20, 'offset' => 0),$filterParams['filter']));

        if (isset($_GET['include_messages']) && $_GET['include_messages'] == 'true' && !empty($mails)) {
            $messages = \erLhcoreClassModelMailconvMessage::getList(array('limit' => 100000,'sort' => 'id ASC','filterin' => array('conversation_id' => array_keys($mails))));
            foreach ($messages as $message) {
                if (!is_array($mails[$message->conversation_id]->messages)) {
                    $mails[$message->conversation_id]->messages = array();
                }
                $mails[$message->conversation_id]->messages[] = $message;
            }
        }

        $prefillFields = array();

        if (isset($_GET['prefill_fields'])){
            $prefillFields = explode(',',str_replace(' ','',$_GET['prefill_fields']));
        }

        if (in_array('mailbox',$prefillFields) && !\erLhcoreClassRestAPIHandler::hasAccessTo('lhmailconv','mailbox_manage')) {
            throw new \Exception('You do not have access to manage mailbox! \'lhmailconv\',\'mailbox_manage\'');
        }

        $ignoreFields = array();
        if (isset($_GET['ignore_fields'])){
            $ignoreFields = explode(',',str_replace(' ','',$_GET['ignore_fields']));
        }

        if (!empty($prefillFields) || !empty($ignoreFields)) {
            \erLhcoreClassChat::prefillGetAttributes($mails, $prefillFields, $ignoreFields, array('clean_ignore' => true, 'do_not_clean' => true));
        }

        $mailsCount = 0;

        if (isset($_GET['count_records']) && $_GET['count_records'] == 'true') {
            $mailsCount = \erLhcoreClassModelMailconvConversation::getCount($filterParams['filter']);
        }

        return array(
            'filter' => $filterParams['filter'],
            'list_count' => $mailsCount,
            'error' => false,
            'list' => array_values($mails),
        );
    }
}
