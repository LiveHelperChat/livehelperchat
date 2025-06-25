<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($Params['user_parameters']['id']);

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv) )
    {
        if ((int)$Params['user_parameters']['status'] == 1) {
            $conv->status = erLhcoreClassModelMailconvConversation::STATUS_ACTIVE;
        } else {
            $conv->status = erLhcoreClassModelMailconvConversation::STATUS_PENDING;
            $conv->user_id = 0;
            $conv->accept_time = 0;
            $conv->wait_time = 0;
        }

        $conv->updateThis(['update' => ['status','user_id','accept_time','wait_time']]);

        $db->commit();

        erLhcoreClassChat::prefillGetAttributesObject($conv,
            erLhcoreClassMailconv::$conversationAttributes,
            erLhcoreClassMailconv::$conversationAttributesRemove
        );

        if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
            if ($conv->from_address == $conv->from_name) {
                $conv->from_name = \LiveHelperChat\Helpers\Anonymizer::maskEmail($conv->from_name);
            }
            $conv->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($conv->from_address);
        }

        if (isset($conv->phone)) {
            $conv->phone_front = $conv->phone;

            if ($conv->phone != '' && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_see_unhidden')) {
                $conv->phone_front = \LiveHelperChat\Helpers\Anonymizer::maskPhone($conv->phone);
                if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','have_phone_link')){
                    $conv->phone = '';
                }
            }
        }

        echo json_encode([
            'conv' => $conv
        ],\JSON_INVALID_UTF8_IGNORE);

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to write conversation.'));
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>