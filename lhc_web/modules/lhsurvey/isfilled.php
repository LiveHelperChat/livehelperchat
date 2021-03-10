<?php

erLhcoreClassRestAPIHandler::setHeaders();

$isFilled = false;

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat) {

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash == $Params['user_parameters']['hash']) {
        $survey = erLhAbstractModelSurvey::fetch($Params['user_parameters']['survey']);
        if ($survey instanceof erLhAbstractModelSurvey) {
            $surveyItem = erLhAbstractModelSurveyItem::getInstance($chat, $survey);
            if ($surveyItem->is_filled == true) {
                $isFilled = true;
            }
        }
    }
}

echo json_encode($isFilled);
exit;