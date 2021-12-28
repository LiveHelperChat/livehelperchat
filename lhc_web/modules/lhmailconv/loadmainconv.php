<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($Params['user_parameters']['id']);

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
        $mcOptionsData = (array)$mcOptions->data;

        $messages = erLhcoreClassModelMailconvMessage::getList(array('sort' => 'udate ASC', 'filter' => ['conversation_id' => $conv->id]));

        $userData = $currentUser->getUserData();

        $operatorChanged = false;
        $chatAccepted = false;
        $canWrite = erLhcoreClassChat::hasAccessToWrite($conv);

        if ($Params['user_parameters_unordered']['mode'] == 'normal' && $userData->invisible_mode == 0 && $canWrite) {

            if ($conv->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING && $conv->user_id != $userData->id && !$currentUser->hasAccessTo('lhchat','open_all')) {
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
                ($chatAccepted || $operatorChanged) && erLhcoreClassMailconvWorkflow::changePersonalMailbox($conv,$conv->user_id);
                $conv->updateThis();
            }
        }

        if ($operatorChanged || $chatAccepted) {
            foreach ($messages as $indexMessage => $message) {
                if ($message->user_id == 0 && $message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED && $message->status != erLhcoreClassModelMailconvMessage::STATUS_ACTIVE)
                {
                    $message->accept_time = time();
                    $message->wait_time = $message->accept_time - $message->ctime;
                    $message->status = erLhcoreClassModelMailconvMessage::STATUS_ACTIVE;
                    $message->updateThis();
                    $messages[$indexMessage] = $message;
                }
            }
        }

        $remarks = erLhcoreClassModelMailconvRemarks::getInstance($conv->customer_email, false)->remarks;

        erLhcoreClassChat::prefillGetAttributesObject($conv,
            erLhcoreClassMailconv::$conversationAttributes,
            erLhcoreClassMailconv::$conversationAttributesRemove
        );

        erLhcoreClassChat::prefillGetAttributes($messages,
            erLhcoreClassMailconv::$messagesAttributes,
            erLhcoreClassMailconv::$messagesAttributesRemove
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
            'print preview importcss searchreplace autolink save autosave directionality visualblocks visualchars fullscreen media codesample charmap pagebreak nonbreaking anchor toc advlist lists wordcount textpattern noneditable help charmap emoticons'
        ];

        if (isset($mcOptionsData['mce_plugins']) && $mcOptionsData['mce_plugins'] != '') {
            $mcePlugins = json_decode($mcOptionsData['mce_plugins'], true);
        }

        echo json_encode(array(
            'conv' => $conv,
            'customer_remarks' => $remarks,
            'messages' => array_values($messages),
            'moptions' => [
                'lang_dir' => erLhcoreClassDesign::design('images/flags'),
                'can_write' => $canWrite,
                'fop_op' => $data['ft_op'],
                'fop_size' => $data['fs_max'] * 1024,
                'files_enabled' => $currentUser->hasAccessTo('lhmailconv', 'allow_attach_files'),
                'hide_recipients' => !$currentUser->hasAccessTo('lhmailconv', 'manage_reply_recipients'),
                'send_as_new' => $currentUser->hasAccessTo('lhmailconv', 'send_as_new'),
                'mce_plugins' => $mcePlugins,
                'mce_toolbar' => $mceToolbar,
                'tiny_mce_path' => erLhcoreClassDesign::design('js/tinymce/js/tinymce/tinymce.min.js')
            ]
        ));

        $db->commit();
    } else {
        throw new Exception("No permission to read conversation.");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>