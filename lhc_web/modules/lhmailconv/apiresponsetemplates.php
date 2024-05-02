<?php

erLhcoreClassRestAPIHandler::setHeaders();

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$conv = $message->conversation;

if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) ) {

    $response = [];

    foreach (erLhcoreClassModelMailconvResponseTemplate::getList(['limit' => false, 'sort' => '`name` ASC', 'customfilter' => ['`disabled` = 0 AND (`dep_id` = -1 OR `id` IN (SELECT `template_id` FROM `lhc_mailconv_response_template_dep` WHERE `dep_id` = ' . (int)$conv->dep_id . '))']]) as $responseTemplate) {
        $response[] = [
            'title' => $responseTemplate->name,
            'content' => str_replace([
                '{operator}',
                '{department}'
            ],[
                $currentUser->getUserData()->name_official,
                $conv->department_name
            ],($responseTemplate->template != '' ? $responseTemplate->template : nl2br($responseTemplate->template_plain))),
            'description' => ''
        ];
    }
}

echo json_encode($response,\JSON_INVALID_UTF8_IGNORE);
exit;

?>