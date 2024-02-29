<?php

session_write_close();

header('content-type: application/json; charset=utf-8');

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => true, 'result' => 'Invalid CSRF Token' ));
	exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {

    $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

    erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

    // Chat can be closed only by owner
    if ($chat->user_id == $currentUser->getUserID() || ($currentUser->hasAccessTo('lhchat','allowcloseremote') && erLhcoreClassChat::hasAccessToWrite($chat)))
    {
        $userData = $currentUser->getUserData(true);

        if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','closerelated')) {
            $relatedItems = json_decode(file_get_contents('php://input'),true);

            // Close related e-mail conversations if required
            if (is_array($relatedItems) && isset($relatedItems['closemailconfirm']) && !empty($relatedItems['closemailconfirm'])) {
                $db = ezcDbInstance::get();
                $db->beginTransaction();
                foreach ($relatedItems['closemailconfirm'] as $relatedConversationId) {
                    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($relatedConversationId);

                    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv))
                    {
                        if ($conv->status !== erLhcoreClassModelMailconvConversation::STATUS_CLOSED) {

                            $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options_general');
                            $data = (array)$mcOptions->data;

                            // Add subject if required
                            if (isset($data['subject_id']) && $data['subject_id'] > 0) {

                                // Find first message always and add subject to it.
                                $message = erLhcoreClassModelMailconvMessage::findOne([
                                    'sort' => '`id` ASC',
                                    'filter' => [
                                        'conversation_id' => $conv->id
                                    ]
                                ]);

                                if (is_object($message)) {
                                    $subjectChat = erLhcoreClassModelMailconvMessageSubject::findOne(array('filter' => array(
                                        'message_id' => $message->id,
                                        'subject_id' => (int)$data['subject_id']
                                    )));

                                    if (!($subjectChat instanceof erLhcoreClassModelMailconvMessageSubject)) {
                                        $subjectChat = new erLhcoreClassModelMailconvMessageSubject();
                                    }

                                    $subjectChat->message_id = $message->id;
                                    $subjectChat->conversation_id = $message->conversation_id;
                                    $subjectChat->subject_id = (int)$data['subject_id'];
                                    $subjectChat->saveThis();
                                }
                            }

                            $msg = new erLhcoreClassModelMailconvMessageInternal();
                            $msg->chat_id = $conv->id;
                            $msg->user_id = -1;
                            $msg->name_support = $userData->name_support;
                            $msg->msg = (string)$msg->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','has closed a conversation from chat!') . ' [' .  $chat->id . ']';
                            $msg->saveThis();
                        }

                        erLhcoreClassMailconvWorkflow::closeConversation([
                            'conv' => & $conv,
                            'user_id' => $currentUser->getUserID(),
                            'force_user_change' => true
                        ]);
                    }
                }
                $db->commit();
            }
        }

        $chat->support_informed = 1;
        $chat->has_unread_messages = 0;
        $chat->unread_messages_informed = 0;

        erLhcoreClassChatHelper::closeChat(array(
            'user' => $userData,
            'chat' => $chat,
        ));
        echo json_encode(array('error' => false, 'result' => 'ok' ));
    } else {
        echo json_encode(array('error' => true, 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','You do not have permission to close this chat!')));
    }

    $db->commit();

} catch (Exception $e) {
    $db->rollback();

    erLhcoreClassLog::write($e->getMessage() . "\n" . $e->getTraceAsString(),
        ezcLog::SUCCESS_AUDIT,
        array(
            'source' => 'lhc',
            'category' => 'update_active_chats',
            'line' => __LINE__,
            'file' => __FILE__,
            'object_id' => $currentUser->getUserID()
        )
    );

    echo json_encode(array('error' => true, 'result' => $e->getMessage() ));
}

exit;

?>