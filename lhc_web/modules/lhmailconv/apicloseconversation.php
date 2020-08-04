<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($Params['user_parameters']['id']);

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conv, 'user_id' => $currentUser->getUserID()]);

        $messages = erLhcoreClassModelMailconvMessage::getList(array('sort' => 'udate ASC', 'filter' => ['conversation_id' => $conv->id]));

        erLhcoreClassChat::prefillGetAttributes($messages, array(
            'udate_front',
            'udate_ago',
            'body_front',
            'plain_user_name',
            'accept_time_front',
            'lr_time_front',
            'wait_time_pending',
            'wait_time_response',
            'interaction_time_duration',
            'cls_time_front',
            'to_data_front',
            'reply_to_data_front',
            'cc_data_front',
            'bcc_data_front',
        ), array('user','conversation'));

        erLhcoreClassChat::prefillGetAttributesObject($conv, array(
            'plain_user_name',
            'udate_front',
            'department_name',
            'accept_time_front',
            'cls_time_front',
            'wait_time_pending',
            'wait_time_response',
            'lr_time_front',
            'interaction_time_duration',
        ), array('department','user'));

        $db->commit();

        echo json_encode(['conv' => $conv, 'messages' =>  array_values($messages)]);

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read conversation.'));
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>