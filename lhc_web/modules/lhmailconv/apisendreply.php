<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    $response = null;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv) && erLhcoreClassChat::hasAccessToRead($conv) ) {
        $requestPayload = json_decode(file_get_contents('php://input'),true);

        // Do not send a reply if it contains sensitive information
        if ((int)erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value == 1 && isset($requestPayload['content']) && $requestPayload['content'] != '') {

            $maskResult = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessageHtml($requestPayload['content'], [
                'dep_id' => $conv->dep_id,
                'type' => \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_OPERATOR_EMAIL,
                'return_array' => true
            ]);

            if ($maskResult['message'] !== $requestPayload['content']) {

                $matchedEntities = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::getMatchedEntities();
                $matchedRules = array_map(function($entityType) {
                    $entityName = \LiveHelperChat\Helpers\BBCode\Mask::getEntityName($entityType);

                    return $entityName !== '' ? $entityName : \LiveHelperChat\Helpers\BBCode\Mask::getCategory($entityType);
                }, $matchedEntities);

                $matchedRules = array_values(array_unique($matchedRules));

                $errorMessage = !empty($maskResult['warning'])
                    ? $maskResult['warning']
                    : erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','For privacy protection, the message was not sent because it contains sensitive information (PII).');

                $guardrails = [
                    'warning' => $errorMessage,
                    'badges' => array_map(['\\LiveHelperChat\\Helpers\\BBCode\\Mask', 'render'], $matchedEntities)
                ];

                if (!empty($matchedRules)) {
                    $errorMessage .= ' ' . implode(', ', $matchedRules);
                }

                http_response_code(400);
                echo json_encode([
                    'send' => false,
                    'send_tried' => true,
                    'masked' => $maskResult['message'],
                    'guardrails' => $guardrails,
                    'errors' => [
                        'content' => $errorMessage
                    ]
                ], \JSON_INVALID_UTF8_IGNORE);
                exit;
            }
        }

        erLhcoreClassMailconvValidator::sendReply($requestPayload, $response, $message, $currentUser->getUserID());
    }

    // We have tried to send an e-mail
    $response['send_tried'] = true;

    if (isset($response['send']) && $response['send'] == true) {

        $conv->lr_time = time();
        $conv->updateThis(['update' => ['lr_time']]);

        // Change to explicit action support
        if (isset($requestPayload['status'])) {
            if ($requestPayload['status'] == 2) {
                erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conv, 'user_id' => $currentUser->getUserID()]);
            } elseif ($requestPayload['status'] == 1) {
                $conv->status = 1;
                $conv->updateThis(['update' => ['status']]);
            } elseif ($requestPayload['status'] == 0) {
                $conv->status = 0;
                $conv->updateThis(['update' => ['status']]);
            }
        }

        $response['conv_status'] = $conv->status;

        echo json_encode($response,\JSON_INVALID_UTF8_IGNORE);
    } else {
        http_response_code(400);
        echo json_encode($response,\JSON_INVALID_UTF8_IGNORE);
    }

} catch (Exception $e) {
    http_response_code(400);

    $errorMessage = $e->getMessage();

    if (is_object($message) && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
        $errorMessage = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage($errorMessage, array('dep_id' => $message->dep_id, 'type' => \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_OPERATOR_EMAIL));
    }
    
    echo json_encode(['send_tried' => true, 'errors' => ['general' => $errorMessage]]);
}

exit;

?>