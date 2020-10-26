<?php

try {

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        erLhcoreClassChat::prefillGetAttributesObject($message, array(
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
            'attachments',
            'bcc_data_front',
            'subjects'
        ), array('user','conversation'));

        echo json_encode(['message' => $message]);
        exit;

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read conversation.'));
    }

} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/errors/adminchatnopermission.tpl.php');
    echo $tpl->fetch();
    exit;
}

?>