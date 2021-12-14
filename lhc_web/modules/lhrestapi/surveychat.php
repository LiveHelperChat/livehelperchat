<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhrestapi', 'survey')) {
        throw new Exception('You do not have permission. `lhrestapi`, `survey` is required.');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);

        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        $survey = erLhAbstractModelSurvey::fetch((int)$Params['user_parameters']['survey_id']);

        if (!($survey instanceof erLhAbstractModelSurvey)) {
            throw new Exception('Survey could not be found!');
        }

        $requestBody = json_decode(file_get_contents('php://input'),true);

        $presentSurvey = erLhAbstractModelSurveyItem::findOne(['filter' => ['chat_id' => $chat->id, 'survey_id' => $survey->id]]);

        if (!($presentSurvey instanceof erLhAbstractModelSurveyItem)){
            $presentSurvey = new erLhAbstractModelSurveyItem();
            $presentSurvey->chat_id = $chat->id;
            $presentSurvey->survey_id = $survey->id;
            $presentSurvey->dep_id = $chat->dep_id;
            $presentSurvey->user_id = $chat->user_id;
            $presentSurvey->ftime = time();
        }

        $attributes = [];
        foreach ($requestBody as $attr => $value) {
            if (strpos($attr,'max_stars_') !== false) {
                $attributes[$attr . 'Evaluate'] = $value;
            } elseif (strpos($attr,'question_options_') !== false) {
                $attributes[$attr . 'EvaluateOption'] = $value;
            } elseif (strpos($attr,'question_plain_') !== false) {
                $attributes[$attr . 'Question'] = $value;
            }
        }

        foreach ($requestBody as $attr => $value) {
            if (in_array($attr,['ftime','status','user_id','dep_id'])) {
                $presentSurvey->{$attr} = $value;
            }
        }

        if (!empty($attributes)) {
            $errors = erLhcoreClassSurveyValidator::validateSurvey($presentSurvey, $survey, $attributes);
        } else {
            $errors = [];
        }

        if (!empty($errors)) {
            http_response_code(403);
            echo erLhcoreClassRestAPIHandler::outputResponse(array(
                'error' => true,
                'type'  => 'validation',
                'result' => $errors
            ));
            exit;
        }

        $presentSurvey->saveThis();

        erLhcoreClassRestAPIHandler::outputResponse(array('result' => true, 'survey' => $presentSurvey->getState()));

    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);

        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        $filter = ['filter' => ['chat_id' => $chat->id]];

        if (is_numeric($Params['user_parameters']['survey_id'])) {
            $filter['filter']['survey_id'] = (int)$Params['user_parameters']['survey_id'];
        }

        erLhcoreClassRestAPIHandler::outputResponse(array('result' => true, 'items' => array_values(erLhAbstractModelSurveyItem::getList($filter))));

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);

        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        $filter = ['filter' => ['chat_id' => $chat->id]];

        if (is_numeric($Params['user_parameters']['survey_id'])) {
            $filter['filter']['survey_id'] = (int)$Params['user_parameters']['survey_id'];
        }

        $deleted = 0;
        foreach (erLhAbstractModelSurveyItem::getList($filter) as $item) {
            $item->removeThis();
            $deleted++;
        }

        erLhcoreClassRestAPIHandler::outputResponse(array('result' => true, 'deleted' => $deleted));
    }

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit;

?>