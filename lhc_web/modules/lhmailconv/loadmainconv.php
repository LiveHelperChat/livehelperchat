<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($Params['user_parameters']['id']);
    $is_archive = false;

    if (!($conv instanceof \erLhcoreClassModelMailconvConversation)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
        if (isset($mailData['mail'])) {
            $conv = $mailData['mail'];
            $is_archive = true;
        }
    }

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        $mailbox = $conv->mailbox;

        $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
        $mcOptionsData = (array)$mcOptions->data;

        if ($is_archive === false) {
            $messages = erLhcoreClassModelMailconvMessage::getList(array('sort' => 'udate ASC', 'filter' => ['conversation_id' => $conv->id]));
        } else {
            $messages = \LiveHelperChat\Models\mailConv\Archive\Message::getList(array('sort' => 'udate ASC', 'filter' => ['conversation_id' => $conv->id]));
        }

        $userData = $currentUser->getUserData();

        $operatorChanged = false;
        $chatAccepted = false;
        $canWrite = erLhcoreClassChat::hasAccessToWrite($conv);

        if ($is_archive === false && $Params['user_parameters_unordered']['mode'] == 'normal' && $userData->invisible_mode == 0 && $canWrite) {

            if (
                ($conv->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING &&
                    $conv->user_id != $userData->id &&
                    !$currentUser->hasAccessTo('lhmailconv','open_all')) &&
                ($conv->user_id != 0 || !$currentUser->hasAccessTo('lhmailconv','open_unassigned_mail'))
            ) {
                throw new Exception('You do not have permission to open all pending mails.');
            }

            if (
                $conv->user_id == 0 &&
                $conv->status != erLhcoreClassModelMailconvConversation::STATUS_CLOSED &&
                $conv->transfer_uid != $currentUser->getUserID() &&
                (!isset($mcOptionsData['disable_auto_owner']) || $mcOptionsData['disable_auto_owner'] == 0)
            ) {
                    $currentUser = erLhcoreClassUser::instance();
                    $conv->user_id = $currentUser->getUserID();
                    $operatorChanged = true;
            }

            // If status is pending change status to active
            if (
                $conv->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING &&
                $conv->transfer_uid != $currentUser->getUserID() &&
                (!isset($mcOptionsData['disable_auto_owner']) || $mcOptionsData['disable_auto_owner'] == 0)
            ) {
                $conv->status = erLhcoreClassModelMailconvConversation::STATUS_ACTIVE;
                $conv->accept_time = time();
                $conv->wait_time = $conv->accept_time - $conv->pnd_time;
                $conv->user_id = $currentUser->getUserID();
                $chatAccepted = true;
            }

            if ($conv->transfer_uid > 0 && (!isset($mcOptionsData['disable_auto_owner']) || $mcOptionsData['disable_auto_owner'] == 0)) {
                erLhcoreClassTransfer::handleTransferredChatOpen($conv, $currentUser->getUserID(), erLhcoreClassModelTransfer::SCOPE_MAIL);
            }

            if (!isset($mcOptionsData['disable_auto_owner']) || $mcOptionsData['disable_auto_owner'] == 0) {

                $conv->updateThis();

                if ($chatAccepted || $operatorChanged) {
                    erLhcoreClassMailconvWorkflow::changePersonalMailbox($conv, $conv->user_id);

                    $conv->updateThis();

                    erLhcoreClassChat::updateActiveChats($conv->user_id);

                    if ($conv->department !== false) {
                        erLhcoreClassChat::updateDepartmentStats($conv->department);
                    }

                    erLhcoreClassMailconvWorkflow::logInteraction($conv->plain_user_name . ' [' . $conv->user_id.'] '.erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has accepted a mail by opening it.'), $conv->plain_user_name, $conv->id);
                }
            }
        }

        if ($operatorChanged || $chatAccepted) {
            foreach ($messages as $indexMessage => $message) {
                if ($message->user_id == 0 && $message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED && $message->status != erLhcoreClassModelMailconvMessage::STATUS_ACTIVE)
                {
                    $message->accept_time = time();
                    $message->wait_time = $message->accept_time - $message->ctime;
                    $message->status = erLhcoreClassModelMailconvMessage::STATUS_ACTIVE;
                    $message->conv_user_id = $conv->user_id;
                    $message->user_id = $conv->user_id;
                    $message->updateThis();
                    $messages[$indexMessage] = $message;
                } else {
                    if ($message->user_id == 0) {
                        $message->user_id = $conv->user_id;
                    }
                    $message->conv_user_id = $conv->user_id;
                    $message->updateThis(['update' => ['conv_user_id', 'user_id']]);
                }
            }
        }

        $remarks = erLhcoreClassModelMailconvRemarks::getInstance($conv->customer_email, false)->remarks;

        erLhcoreClassChat::prefillGetAttributesObject($conv,
            erLhcoreClassMailconv::$conversationAttributes,
            erLhcoreClassMailconv::$conversationAttributesRemove
        );

        $requestPayload = json_decode(file_get_contents('php://input'),true);


        foreach ($messages as $indexMessage => $messageItem) {
            if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
                $messages[$indexMessage]->setSensitive(true);
            }

            if (isset($requestPayload['keyword']) && !empty($requestPayload['keyword']) && is_array($requestPayload['keyword'])) {
                foreach ($requestPayload['keyword'] as $keyword) {
                    $messages[$indexMessage]->subject = str_ireplace($keyword,'🔍'.$keyword.'🔍',$messages[$indexMessage]->subject);
                }
            }
        }

        if (isset($requestPayload['keyword']) && !empty($requestPayload['keyword']) && is_array($requestPayload['keyword'])) {
            foreach ($requestPayload['keyword'] as $keyword) {
                $conv->subject = str_ireplace($keyword, '🔍' . $keyword . '🔍', $conv->subject);
            }
        }

        erLhcoreClassChat::prefillGetAttributes($messages,
            erLhcoreClassMailconv::$messagesAttributesLoaded,
            erLhcoreClassMailconv::$messagesAttributesRemoveLoaded
        );

        $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
        $data = (array)$fileData->data;

        $mceToolbar = 'undo redo | fontselect formatselect fontsizeselect | table | paste pastetext | subscript superscript |'.
            ' bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify '.
            '| lhtemplates lhfiles insertfile image pageembed link anchor codesample | bullist numlist outdent indent | removeformat permanentpen | charmap emoticons | fullscreen print preview paste code | help';

        if (isset($mcOptionsData['mce_toolbar']) && $mcOptionsData['mce_toolbar'] != '') {
            $mceToolbar = $mcOptionsData['mce_toolbar'];
        }

        $mcePlugins = [
            'advlist autolink lists link image charmap print preview anchor image lhfiles',
            'searchreplace visualblocks code fullscreen',
            'media table paste help',
            'print preview importcss searchreplace autolink save directionality visualblocks visualchars fullscreen media codesample charmap pagebreak nonbreaking anchor toc advlist lists wordcount textpattern noneditable help charmap emoticons'
        ];

        if (isset($mcOptionsData['mce_plugins']) && $mcOptionsData['mce_plugins'] != '') {
            $mcePlugins = json_decode($mcOptionsData['mce_plugins'], true);
        }

        if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
            $conv->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($conv->from_address);
        }

        if (isset($conv->phone)) {
            $conv->phone_front = $conv->phone;

            if ($conv->phone != '' && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_see_unhidden')) {
                $conv->phone_front = \LiveHelperChat\Helpers\Anonymizer::maskPhone($conv->phone);
                if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','have_phone_link')) {
                    $conv->phone = '';
                }
            }
        }

        $editorOptions = array(
            'conv' => $conv,
            'customer_remarks' => $remarks,
            'messages' => array_values($messages),
            'moptions' => [
                'is_archive' => $is_archive,
                'is_blocked' => erLhcoreClassModelChatBlockedUser::isBlocked(array('email_conv' => $conv->from_address)),
                'lang_dir' => erLhcoreClassDesign::design('images/flags'),
                'skip_images' => ((isset($mcOptionsData['skip_images']) && $mcOptionsData['skip_images'] == 1) || !$currentUser->hasAccessTo('lhmailconv','include_images')),
                'image_skipped_text' => ((isset($mcOptionsData['image_skipped_text']) && $mcOptionsData['image_skipped_text'] != '') ? $mcOptionsData['image_skipped_text'] : '[img]'),
                'can_write' => ($is_archive === false && $canWrite && $mailbox->active == 1),
                'can_close' => ($is_archive === false && $canWrite),
                'can_forward' => $currentUser->hasAccessTo('lhmailconv', 'send_as_forward'),
                'can_change_mailbox' => $currentUser->hasAccessTo('lhmailconv', 'change_mailbox'),
                'fop_op' => $data['ft_op'],
                'fop_size' => $data['fs_max'] * 1024,
                'files_enabled' => $currentUser->hasAccessTo('lhmailconv', 'allow_attach_files'),
                'hide_recipients' => !$currentUser->hasAccessTo('lhmailconv', 'manage_reply_recipients'),
                'send_as_new' => $currentUser->hasAccessTo('lhmailconv', 'send_as_new'),
                'can_download' => $currentUser->hasAccessTo('lhmailconv', 'can_download'),
                'mce_plugins' => $mcePlugins,
                'mce_toolbar' => $mceToolbar,
                'mail_links' => [],
                'cache_version_plugin' => (int)erConfigClassLhConfig::getInstance()->getSetting('site', 'static_version', false),
                'tiny_mce_path' => erLhcoreClassDesign::designJSStatic('js/tinymce/js/tinymce/tinymce.min.js')
            ]
        );

        if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
            foreach ($editorOptions['messages'] as $indexMessage => $messageItem) {
                if (!isset($messageItem->response_type) || $messageItem->response_type !== erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
                    $editorOptions['messages'][$indexMessage]->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($editorOptions['messages'][$indexMessage]->from_address);
                }
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.editor_options',array('options' => & $editorOptions));

        echo json_encode($editorOptions,\JSON_INVALID_UTF8_IGNORE);

        $db->commit();

        if (!$currentUser->hasAccessTo('lhaudit','ignore_view_actions')) {
            erLhcoreClassLog::write(0,
                ezcLog::SUCCESS_AUDIT,
                array(
                    'source' => 'lhc',
                    'category' => $Params['user_parameters_unordered']['mode'] == 'normal' ? 'mail_open' : 'mail_view',
                    'line' => __LINE__,
                    'file' => __FILE__,
                    'object_id' => $conv->id,
                    'user_id' => $currentUser->getUserID()
                )
            );
        }

    } else {
        throw new Exception("No permission to read conversation.");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ),\JSON_INVALID_UTF8_IGNORE);
}


exit;

?>