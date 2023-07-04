<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$payload = json_decode(file_get_contents('php://input'),true);

try {

    $db = ezcDbInstance::get();

    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($payload['chat_id']);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    $survey = erLhAbstractModelSurvey::fetch($payload['survey_id']);

    $surveyItem = erLhAbstractModelSurveyItem::getInstance($chat, $survey);

    $errors = erLhcoreClassSurveyValidator::validateSurvey($surveyItem, $survey, $payload);

    if ($chat->hash !== $payload['hash'])
    {
        $errors[] = 'No permission to edit survey!';
    }

    $tpl = erLhcoreClassTemplate::getInstance( 'lhsurvey/submit_fill_inline.tpl.php');
    $tpl->setArray([
            'chat' => $chat,
            'survey' => $survey,
            'stored' => true
        ]);

    if (empty($errors)) {

        $surveyItem->saveOrUpdate();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('survey.filled', array('chat' => & $chat, 'survey' => $survey, 'survey_item' => & $surveyItem));

        $tpl->set('stored', true);

    } else {
        $tpl->set('errors', $errors);
    }

    echo json_encode(['result' => $tpl->fetch(), 'is_valid' => empty($errors)]);

    $db->commit();

} catch (Exception $e) {

    $db->rollback();
    
    // Store log
    erLhcoreClassLog::write($e->getMessage() . ' - ' . $e->getTraceAsString(),
        ezcLog::SUCCESS_AUDIT,
        array(
            'source' => 'lhc',
            'category' => 'store',
            'line' => $e->getLine(),
            'file' => 'fillinline.php',
            'object_id' => $payload['chat_id']
        )
    );
}

exit;