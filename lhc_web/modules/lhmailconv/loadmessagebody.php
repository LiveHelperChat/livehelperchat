<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id_conv']);

    $is_archive = false;

    if (!($conv instanceof \erLhcoreClassModelMailconvConversation)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
        if (isset($mailData['mail'])) {
            $conv = $mailData['mail'];
            $is_archive = true;
            $message = \LiveHelperChat\Models\mailConv\Archive\Message::fetchAndLock($Params['user_parameters']['id']);
        }
    } else {
        $message = erLhcoreClassModelMailconvMessage::fetchAndLock($Params['user_parameters']['id']);
        $conv = $message->conversation;
    }

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {

        $returnAttributes = [];

        erLhcoreClassChat::prefillGetAttributesObject($conv,
            erLhcoreClassMailconv::$conversationAttributes,
            erLhcoreClassMailconv::$conversationAttributesRemove
        );

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

        $returnAttributes['conv'] = $conv;

        if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
            $message->setSensitive(true);
        }

        erLhcoreClassChat::prefillGetAttributesObject($message,
            erLhcoreClassMailconv::$messagesAttributes,
            erLhcoreClassMailconv::$messagesAttributesRemove
        );

        if (!isset($message->body_front)) {
            $message->body_front = "";
        }

        if (!isset($message->body)) {
            $message->body = "";
        }

        if (!isset($message->alt_body)) {
            $message->alt_body = "";
        }

        if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
            if (!isset($message->response_type) || $message->response_type !== erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
                $message->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($message->from_address);
            }
        }

        $requestPayload = json_decode(file_get_contents('php://input'),true);

        if (isset($requestPayload['keyword']) && !empty($requestPayload['keyword']) && is_array($requestPayload['keyword'])) {
            foreach ($requestPayload['keyword'] as $keyword) {
                $message->body_front = str_ireplace($keyword,'<span class="bg-warning text-dark rounded p-1 d-inline-block"><em><strong><span style="font-size:14pt;">'.$keyword.'</span></strong></em></span>',$message->body_front);
                $message->subject = str_ireplace($keyword,'🔍'.$keyword.'🔍',$message->subject);
            }
        }


        $returnAttributes['message'] = $message;

        $db->commit();

        echo json_encode($returnAttributes,\JSON_INVALID_UTF8_IGNORE);

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